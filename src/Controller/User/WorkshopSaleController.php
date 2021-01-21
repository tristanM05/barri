<?php

namespace App\Controller\User;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            'dateNow' => $date_now,
            // 'form' => $form->createView(),
            'products' => $products
        ]);
    }

    /**
     * @Route("/user/sold_article", name="user_sold_article_v2")
     */
    public function addArticle(SessionInterface $session, ArticleRepository $repo, EntityManagerInterface $manager){

        extract($_POST);
        // $number = $repo->findOneBy(['number' => $this->getNumber()]);
        $client = $this->getUser();
        $article = $repo->findOneBy(['number' => $number, 'client' => $client]);
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
                $roro = $_POST[$keepSpecialPrice];
                if ($roro) {
                    $_POST[$keepSpecialPrice] = $_POST[$keepSpecialPrice];
                }
                
                $cartWithData[] = [
                    'article' => $repo->find($id),
                    'quantity' => $quantity,
                    'specialPrice' => $_POST[$keepSpecialPrice]
                ];
                $article = $repo->findBy(['id' => $id]);
                $article[0]->setSpecialprice($_POST[$keepSpecialPrice]);
                $manager->persist($article[0]);
                
            }
            $products = $repo->findBy(['client' => $client]);
    
        return $this->render('User/workshop_sale/workshop_sale.html.twig', [
            'articles' => $cartWithData,
            'dateNow' => $date_now,
            'products' =>$products
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
    public function soldArticle(SessionInterface $session, ArticleRepository $repo, ProductStatusRepository $repo_status, EntityManagerInterface $manager){

        extract($_POST);
        // dd($_POST);
        $product_status = $repo_status->findOneBy(['wording' => 'vendu']);
        $cart = $session->get('cart',[]);

        foreach ($cart as $key => $value) {
            $article = $repo->findOneBy(['id' => $key]);
            $datede = 'datede'.$key;
            $date = date_create_from_format('j/m/Y', $_POST[$datede]);
            if ($_POST[$key] == "") {
                $price = null;
            } else {
                $price = $_POST[$key];
            }

            $article
                    ->setLeftdate($date)
                    ->setSpecialprice($price)
                    ->setProductStatus($product_status);

            $manager->persist($article);
            $manager->flush();
            unset($cart[$key]);
        }

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
