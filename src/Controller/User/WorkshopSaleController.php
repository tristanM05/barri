<?php

namespace App\Controller\User;

use App\Entity\Ventes;
use App\Entity\Decaissement;
use App\Entity\Encaissement;
use App\Repository\ArticleRepository;
use App\Service\DashboardUserService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\JournalRepository;
use App\Repository\LotsRepository;
use App\Repository\ProductStatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WorkshopSaleController extends AbstractController
{
    /**
     * @Route("/user/workshop_sale", name="user_workshop_sale")
     */
    public function index(SessionInterface $session, ArticleRepository $repo){

        $cart = $session->get('cart',[]);
        $date_now = date('d/m/Y');
        $client = $this->getUser();
        
        $date = new \DateTime('now');
        $date->setTime(0,0,0);

        $cartWithData = [];
        foreach($cart as $id => $quantity){
            $cartWithData[] = [
                'article' => $repo->find($id),
                'quantity' => $quantity,
            ];
        }
        $products = $repo->findBy(['client' => $client]);

        return $this->render('User/workshop_sale/workshop_sale.html.twig', [
            'articles' => $cartWithData,
            'somme' => null,
            'somme2' => null,
            'dateNow' => $date_now,
            'products' => $products,
            'date' => $date,
        ]);
    }

    /**
     * @Route("/user/sold_article", name="user_sold_article_v2")
     */
    public function addArticle(SessionInterface $session, ArticleRepository $repo, Request $req, EntityManagerInterface $manager, DashboardUserService $service){

        extract($_POST);
        // $number = $repo->findOneBy(['number' => $this->getNumber()]);
        $client = $this->getUser();
        $article = $repo->findOneBy(['number' => $number, 'client' => $client]);
        // $lots = $lots_repo->findBy(['article' => $article]);
        $date = new \DateTime('now');
        $date->setTime(0,0,0);
        $date_now = date('d/m/Y');

        if ($article) {
                if ($article->getProductStatus() != 'en vente') {
                    $this->addFlash('danger', 'Cet article n\'est plus en vente');
                    return $this->redirectToRoute('user_workshop_sale');
                }
                $this->addFlash('success', 'L\'article à bien été ajouté au panier');
                $cart = $session->get('cart',[]);

                $cart[$article->getId()] = 1;
                $session->set('cart', $cart);
        } else {
            $this->addFlash('danger', 'Aucun article n\'est associé à ce numéro');
            return $this->redirectToRoute('user_workshop_sale');
        }
        
            $cart = $session->get('cart',[]);

            $cartWithData = [];
            

            foreach($cart as $id => $quantity){
                $keepSpecialPrice = 'keepSpecialPrice'.$id;
                $keepQuantity = 'keepQuantity'.$id;
                $roro = $_POST[$keepSpecialPrice];
                $roro2 = $_POST[$keepQuantity];
                if ($roro) {
                    $_POST[$keepSpecialPrice] = $_POST[$keepSpecialPrice];
                }
                if ($roro2) {
                    $_POST[$keepQuantity] = $_POST[$keepQuantity];
                }
                
                $cartWithData[] = [
                    'article' => $repo->find($id),
                    'quantity' => $quantity,
                    'specialPrice' => $_POST[$keepSpecialPrice],
                    'quantity' => $_POST[$keepQuantity]
                ];
                $article = $repo->findBy(['id' => $id]);
                $article[0]->setSpecialprice($_POST[$keepSpecialPrice]);
                $article[0]->setQuantity($_POST[$keepQuantity]);
                $manager->persist($article[0]);
                
            }
            $products = $repo->findBy(['client' => $client]);            
    
        return $this->render('User/workshop_sale/workshop_sale.html.twig', [
            'articles' => $cartWithData,
            // 'lots' => $lots,
            'dateNow' => $date_now,
            'products' =>$products,
            'date' => $date,
        ]);
    }

    /**
     * @Route("/user/cart/remove/article/{id}", name="user_cart_remove_article")
     */
    public function removeCartArticle($id, SessionInterface $session){

        $cart = $session->get('cart',[]);

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('user_workshop_sale');
    }

    /**
     * @Route("/user/sold/article", name="user_sold_article")
     */
    public function soldArticle(SessionInterface $session, ArticleRepository $repo, ProductStatusRepository $repo_status, EntityManagerInterface $manager, DashboardUserService $service, JournalRepository $journal_repo, LotsRepository $lot_repo){

        extract($_POST);
        // dd($_POST);
        $product_status = $repo_status->findOneBy(['wording' => 'vendu']);
        $cart = $session->get('cart',[]);

        $now = new \DateTime('now');
        $encaissement = new Encaissement();
        $encaissement->setClient($this->getUser());
        $encaissement->setDate($now);
        
        /** $tab = initialization of the table of prices of all articles **/
        $tab = [];
        foreach ($cart as $key => $value) {
            $article = $repo->findOneBy(['id' => $key]);
            $datede = 'datede'.$key;
            $date = date_create_from_format('j/m/Y', $_POST[$datede]);
            $quantity = 'quantity'.$key;
            $articleQuantity = $_POST[$quantity];
            
            if ($_POST[$key] == "") {
                $price = null;
            } else {
                $price = $_POST[$key];
            }
            
            if($article->getIsLots() != null){
                $lot = $lot_repo->findOneBy(['article' => $key, 'number' => $lotNumber]);
                $lotNumber = $_POST['lotNumber'];
                $lotQuantity = $lot->getQuantity();
                if($article->getQuantity()-$articleQuantity > 0){
                    // dd($lotQuantity);
                    $article
                    ->setLeftdate($date)
                    ->setQuantity($article->getQuantity()-$articleQuantity)
                    ->setTotalPrice($article->getQuantity()*$article->getReferenceprice());
                    if($lotQuantity - $articleQuantity >= 0){
                        $lot->setQuantity($lotQuantity - $articleQuantity);
                    }elseif($lotQuantity - $articleQuantity < 0){
                        $lotNumber2 = $_POST['lotNumber2'];
                        $lot2 = $lot_repo->findOneBy(['article' => $key, 'number' => $lotNumber2]);
                        $lotQuantity2 = $lot2->getQuantity();
                        $reste = $articleQuantity - $lotQuantity;
                        $lot->setQuantity($lotQuantity - $articleQuantity + $reste);
                        $lot2->setQuantity($lotQuantity2 - $reste);
                        $manager->persist($lot2);
                    }
                }else{
                    $article
                    ->setLeftdate($date) 
                    ->setQuantity($article->getQuantity()-$articleQuantity)
                    ->setTotalPrice($article->getQuantity()*$article->getReferenceprice())
                    ->setProductStatus($product_status);
                    if($lotQuantity - $articleQuantity >= 0){
                        $lot->setQuantity($lotQuantity - $articleQuantity);
                    }elseif($lotQuantity - $articleQuantity < 0){
                        $lotNumber2 = $_POST['lotNumber2'];
                        $lot2 = $lot_repo->findOneBy(['article' => $key, 'number' => $lotNumber2]);
                        $lotQuantity2 = $lot2->getQuantity();
                        $reste = $articleQuantity - $lotQuantity;
                        $lot->setQuantity($lotQuantity - $articleQuantity + $reste);
                        $lot2->setQuantity($lotQuantity2 - $reste);
                        $manager->persist($lot2);
                    }
                }
                $manager->persist($lot);
            }else{
                if($article->getQuantity()-$articleQuantity > 0){
                    // dd($lotQuantity);
                    $article
                    ->setLeftdate($date)
                    ->setQuantity($article->getQuantity()-$articleQuantity)
                    ->setTotalPrice($article->getQuantity()*$article->getReferenceprice());
                }else{
                    $article
                    ->setLeftdate($date) 
                    ->setQuantity($article->getQuantity()-$articleQuantity)
                    ->setTotalPrice($article->getQuantity()*$article->getReferenceprice())
                    ->setProductStatus($product_status);
                }
            }
                
                $vente = new Ventes();
                
                if($price == null){
                    $vente
                    ->setDateVente($date)
                    ->setArticle($article)
                        ->setClient($this->getUser())
                        ->setQuantity($articleQuantity)
                        ->setPrice($articleQuantity*$article->getReferenceprice())
                        ->setEncaissement($encaissement);
                    $price = $vente->getPrice();
                    $tab[] = $price;
                    }else{
                        $vente
                        ->setDateVente($date)
                        ->setArticle($article)
                        ->setClient($this->getUser())
                        ->setQuantity($articleQuantity)
                        ->setPrice($articleQuantity*$price)
                        ->setEncaissement($encaissement);
                    $price = $vente->getPrice();
                    $tab[] = $price;
                    }

                    $manager->persist($article);
                    $manager->persist($vente);
                    unset($cart[$key]);
                }

                /** COMMENT AND PAYMENT MODE FOR ENCAISSEMENT AND JOURNAL **/
                $sommeCb = $articleQuantity * $article->getReferenceprice();
                $comment = $_POST['comment'];
                // $cb = $_POST['cb'];
                if($_POST['esp'] == null && $_POST['chq'] == null && $_POST['other'] == null && $_POST['cb'] == null){
                    $cb = $sommeCb;
                }else if($_POST['cb'] == null && $_POST['esp'] != null || $_POST['cb'] == null && $_POST['chq'] != null || $_POST['cb'] == null && $_POST['other'] != null){
                    $cb = "0.00";
                }else if($_POST['cb'] != null && $_POST['esp'] != null || $_POST['cb'] != null && $_POST['chq'] != null || $_POST['cb'] != null && $_POST['other'] != null){
                    $cb = $_POST['cb'];                  
                }
               
                if($_POST['esp'] == null){
                    $esp = "0.00";
                }else{
                    $esp = $_POST['esp'];                  
                }
                if($_POST['chq'] == null){
                    $chq = "0.00";
                }else{
                    $chq = $_POST['chq'];                  
                }
                if($_POST['other'] == null){
                    $otherPayment = "0.00";
                }else{
                    $otherPayment = $_POST['other'];                  
                }
                
                // $otherPayment = $_POST['other'];
                /** COMMENT AND PAYMENT MODE FOR ENCAISSEMENT AND JOURNAL **/

                $client =$this->getUser();
                $date = new \DateTime('now');
                $date->setTime(0,0,0);

                $journal = $journal_repo->findOneBy(['date' => $date, 'client' => $client]);

                
                $encaissement->setJournal($journal);
                $encaissement->setComment($comment);
                $encaissement->setIsEnc(1);
                
                /** $tab = price table of all items **/
                $totalEnc = array_sum($tab);
                $encaissement->setTotal($totalEnc);
                
                
                /************************************** ASSIGN PAYMENTS******************************/

                /** GET JOURNAL TOTAL PAYMENT MODE **/
                $totalCb = $journal->getTotalCb();
                $totalEsp = $journal->getTotalEsp();
                $totalChq = $journal->getTotalChq();
                $totalOther = $journal->getTotalOther();
                /** GET JOURNAL TOTAL PAYMENT MODE **/
                
                if($cb == null && $esp == null && $chq == null && $otherPayment == null){
                    /** SET ENCAISSEMENT **/
                    $encaissement->setCb($totalEnc);
                    $encaissement->setEsp($esp);
                    $encaissement->setChq($chq);
                    $encaissement->setOther($otherPayment);
                    /** SET ENCAISSEMENT **/

                    /** SET JOURNAL TOTAL PAYMENT MODE **/
                    $journal->setTotalCb($totalCb + $totalEnc);
                    /** SET JOURNAL TOTAL PAYMENT MODE **/
                }elseif($cb != null && $cb > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($esp != null && $esp > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($chq != null && $chq > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($otherPayment != null && $otherPayment > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($cb != null && $esp != null && $cb + $esp > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($cb != null && $chq != null && $cb + $chq > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($cb != null && $otherPayment != null && $cb + $otherPayment > $totalEnc){
                    $this->addFlash('danger','données incorect');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($cb != null && $esp != null && $chq != null && $otherPayment != null && $cb + $esp + $chq + $otherPayment > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($esp != null && $chq != null && $esp + $chq > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($esp != null && $otherPayment != null && $esp + $otherPayment > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }elseif($chq != null && $otherPayment != null && $chq + $otherPayment > $totalEnc){
                    $this->addFlash('danger','Impossible d\'entrer une valeur supérieure au prix de vente.');
                    return $this->redirectToRoute('user_workshop_sale');
                }else{
                    /** SET ENCAISSEMENT **/
                    $encaissement->setCb($cb);
                    $encaissement->setEsp($esp);
                    $encaissement->setChq($chq);
                    $encaissement->setOther($otherPayment);
                    /** SET ENCAISSEMENT **/

                }
                /** SET JOURNAL TOTAL PAYMENT MODE **/
                if($cb != null){
                    $journal->setTotalCb($totalCb + $cb);
                }
                if($esp != null){
                    $journal->setTotalEsp($totalEsp + $esp);
                }
                if($chq != null){
                    $journal->setTotalChq($totalChq + $chq);
                }
                if($otherPayment != null){
                    $journal->setTotalOther($totalOther + $otherPayment);
                }
                /** SET JOURNAL TOTAL PAYMENT MODE **/

                /*********************************** END ASSIGN PAYMENTS ******************************/

                $manager->persist($encaissement);
                
                /** SET TOTAL OF THE DAY FOR JOURNAL**/
                $totalStart = $journal->getTotalStart();
                $totalEncaissement = $journal->getTotalEnc();
                $totalDecaissement = $journal->getTotalDec();
                $journal->setTotalEnc($totalEncaissement+$totalEnc);
                $journal->setTotalEnd($totalStart+$totalEncaissement+$totalEnc-$totalDecaissement);
                $manager->persist($journal);
                $manager->flush();
                /** SET TOTAL OF THE DAY FOR JOURNAL**/
            

        $session->set('cart', $cart);

        $this->addFlash('success','Les articles ont bien été vendus');

        return $this->redirectToRoute('user_workshop_sale');
    }

    /**
     * @Route("/user/cancel/sale", name="user_cancel_sale")
     */
    public function CancelSale(SessionInterface $session){
        $cart = $session->get('cart',[]);

        foreach ($cart as $key => $value) {
            unset($cart[$key]);
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('user_home');
    }



    



}
