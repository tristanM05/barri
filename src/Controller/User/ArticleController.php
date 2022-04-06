<?php

namespace App\Controller\User;

use DateTime;
use App\Entity\Images;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Fournisseur;
use App\Entity\Lots;
use App\Form\ArticleType;
use App\Form\LotsType;
use App\Repository\ImagesRepository;
use App\Repository\LotsRepository;
use App\Repository\SalepointRepository;
use App\Repository\SubfamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductStatusRepository;
use App\Service\DashboardUserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController{
    
    
    /**
     * Allows a customer to see one of his articles
     * 
    * @Route("/user/article/show/{id}", name="user_article_show")
    */
    public function showArticle(Article $article){
        
        return $this->render('User/workshop_stock/show_article.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * Allows a customer to edit or add one of his articles
     * 
    * @Route("/user/article/add", name="user_article_add")
    * @Route("/user/article/edit/{id}", name="user_article_edit")
    */
    public function editAndAddArticle(Article $article = null, Request $request, EntityManagerInterface $manager, SubfamilyRepository $repo_subfamily,SalepointRepository $repo_salepoint, ProductStatusRepository $repo_product_status, Lots $lots = null, LotsRepository $repo_lots){
        
        $client = $this->getUser();
        $user = $this->getUser()->getId();
        $date = date('Y-m-d');

        if(!$article){
            $subfamily = $repo_subfamily->findOneBy(['client' => $user]);
            $salepoint = $repo_salepoint->findOneBy(['client' => $user]);
            $product_status = $repo_product_status->findOneBy(['wording' => 'en vente']);

            $article = new Article();
            $article->setClient($this->getUser());
            $article->setSubfamily($subfamily);
            $article->setSalepoint($salepoint);
            $article->setProductStatus($product_status);
        }

        $form = $this->createForm(ArticleType::class, $article, ['client' => $user]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $categoryName = $_POST['category'];
            $fournisseurName = $_POST['fournisseur'];

            if($categoryName != null){
                $category = new Category();
                // dd($categoryName);
                $category->setName($categoryName);
                $category->setClient($client);
                $manager->persist($category);
                $article->setCategory($category);
            }
            if($fournisseurName != null){
                $fournisseur = new Fournisseur();
                // dd($categoryName);
                $fournisseur->setName($fournisseurName);
                $fournisseur->setClient($client);
                $manager->persist($fournisseur);
                $article->setFournisseur($fournisseur);
            }
            $edit = $article->getId() !== null;

            // IF NOT ENTERED DATE IN THE FORM AND PRODUCT STATUS IN NOT FOR SALE
            if ($article->getLeftdate() === null && $article->getProductStatus()->getId() !== 1) {
                // $article->setLeftdate(date_create_from_format('j/m/Y',$date));
                $article->setLeftdate(new DateTime('now'));
            }

            // IF PRODUCT STATUS IS CHANGE FOR SALE CLEAR THE LEFT DATE
            if($article->getLeftdate() !== null && $article->getProductStatus()->getId() === 1){
                $article->setLeftdate(null);
            }

            if ($article->getProductiondate() === null) {
                $article->setProductiondate(new DateTime('now'));
            }

            // ADD IMAGES IN CASCADE 
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Images();
                $img->setName($fichier);
                $article->addImage($img);
            }

            // IF QUANTITY = 0 SET PRODUCT_STATUS TO "VENDU" 
            $product_status = $repo_product_status->findOneBy(['wording' => 'vendu']);
            if($article->getQuantity() == '0'){
                $article->setProductStatus($product_status);
            }
            // IF QUANTITY = 0 SET PRODUCT_STATUS TO "VENDU"

            // CALCUL TOTAL PRICE
            $quantity = $article->getQuantity();
            $price = $article->getReferenceprice();
            $totalPrice = $quantity * $price;
            $article->setTotalPrice($totalPrice);
            // CALCUL TOTAL PRICE

            // SET ALERT DATE
            $endDate = $article->getEndDate();
            $limit = $article->getDateLimit();
            if($endDate == null){
            }elseif($limit == null){
            }else{
                $alert = clone $endDate;
                $alert->setTime(0,0,0);
                $alert->modify('-'.$limit. 'day');
                $article->setAlert($alert);
            }
            // SET ALERT DATE      
            
            $manager->persist($article);
            
            $manager->flush();

            $this->addFlash("success",($edit) ? "L'article à bien été modifié" : "L'article à bien été ajouté");
            return $this->redirectToRoute('user_workshop_stock');
        }

        $id = $request->get('id');
            $lots = new Lots();
            $formLots = $this->createForm(LotsType::class, $lots);
            $formLots->handleRequest($request);
            if($article->getId() !== null){
                $quantityArticle = $article->getQuantity();
                if ($formLots->isSubmitted() && $formLots->isValid()) {      
                    $lots->setArticle($article);
                    $article->setQuantity($quantityArticle + $lots->getQuantity());
                    $article->setIsLots(1);
                    $manager->persist($lots);
                    $manager->persist($article);
                    $ArticleQuantityAfterPersist1 = $article->getQuantity();
                    $price = $article->getReferenceprice();
                    $article->setTotalPrice($ArticleQuantityAfterPersist1 * $price);
                    $manager->flush();
                    $this->addFlash("success", "L'article à bien été ajouté");
                    return $this->redirectToRoute('user_workshop_stock');
                }
            }
        

        $allLots = $repo_lots->findBy(['article' => $id]);

        return $this->render('User/workshop_stock/edit_&_add_article.html.twig', [
            'article' => $article,
            'date' => $date,
            'form_article' => $form->createView(),
            'formLots' => $formLots->createView(),
            'isEdit' => $article->getId() !== null,
            'isDuplicate' => null,
            'allLots' => $allLots
        ]);
    }

    /**
    * @Route("/user/article/duplicate/{id}", name="user_duplicate_article")
    */
    public function articleDuplication(Article $article, Request $request, EntityManagerInterface $manager, LotsRepository $repo_lots){

        $user = $this->getUser()->getId();
        $date = date('Y-m-d');
        $new_article = clone $article;

        $form = $this->createForm(ArticleType::class, $new_article, ['client' => $user]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // IF NOT ENTERED DATE IN THE FORM AND PRODUCT STATUS IN NOT FOR SALE
            if ($new_article->getLeftdate() === null && $new_article->getProductStatus()->getId() !== 1) {
                $new_article->setLeftdate(new \Datetime('now'));
            }

            // IF PRODUCT STATUS IS CHANGE FOR SALE CLEAR THE LEFT DATE
            if($new_article->getLeftdate() !== null && $new_article->getProductStatus()->getId() === 1){
                $new_article->setLeftdate(null);
            }
            $manager->persist($new_article);
            $manager->flush();

            $this->addFlash("success","L'article à bien été ajouté");
            return $this->redirectToRoute('user_workshop_stock');
        }

        $id = $request->get('id');
            $lots = new Lots();
            $formLots = $this->createForm(LotsType::class, $lots);
            $formLots->handleRequest($request);
            if($article->getId() !== null){
                $quantityArticle = $article->getQuantity();
                if ($formLots->isSubmitted() && $formLots->isValid()) {      
                    $lots->setArticle($article);
                    $article->setQuantity($quantityArticle + $lots->getQuantity());
                    $article->setIsLots(1);
                    $manager->persist($lots);
                    $manager->persist($article);
                    $ArticleQuantityAfterPersist1 = $article->getQuantity();
                    $price = $article->getReferenceprice();
                    $article->setTotalPrice($ArticleQuantityAfterPersist1 * $price);
                    $manager->flush();
                    $this->addFlash("success", "L'article à bien été ajouté");
                    return $this->redirectToRoute('user_workshop_stock');
                }
            }
        

        $allLots = $repo_lots->findBy(['article' => $id]);
        
        return $this->render('User/workshop_stock/edit_&_add_article.html.twig', [
            'article' => $new_article,
            'date' => $date,
            'form_article' => $form->createView(),
            'isEdit' => $new_article->getId() !== null,
            'isDuplicate' => true,
            'allLots' => $allLots,
            'formLots' => $formLots->createView(),
        ]);
    }


    /**
     * Allows a customer to delete one of his articles
     * 
    * @Route("/user/article/delete/{id}", name="user_article_delete")
    */
    public function deleteArticle(Article $article, Request $request, EntityManagerInterface $manager, ImagesRepository $repo){

        if ($this->isCsrfTokenValid("SUP".$article->getId(),$request->get('_token'))) {

            $images = $repo->findBy(["article" => $article]);

            // $dist = dirname(__DIR__,2) . '/public/uploads/';
            foreach ($images as $image) {
                unlink($this->getParameter('images_directory').'/'.$image->getName());
                $manager->remove($image);
                $manager->flush();
            }

            $manager->remove($article);
            $manager->flush();
            $this->addFlash("success", "L'article à bien été supprimé");
            return $this->redirectToRoute('user_workshop_stock');
        }
        
        $this->addFlash('danger','Une erreur c\'est produite merci de réessayer ultérieurement !');
        return $this->redirectToRoute('user_workshop_stock');
    }

    /**
     * delete lots
     * @Route("/delete/Lots/{id}", name="article_delete_lots")
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @param Lots $lots
     * @param Article $article
     * @return void
     */
    public function deleteLot(Request $req, EntityManagerInterface $manager, Lots $lots){
        $articleLot = $lots->getArticle();
        if ($this->isCsrfTokenValid("SUP".$lots->getId(),$req->get('_token'))) {
            $quantityLot = $lots->getQuantity();
            $quantityArticle = $articleLot->getQuantity();
            $articleLot->setQUantity($quantityArticle - $quantityLot);
            $manager->persist($articleLot);
            $manager->remove($lots);
            $manager->flush();
            $this->addFlash("success", "suppréssion éffectuer");
            return $this->redirectToRoute('user_workshop_stock');
        }
        
        $this->addFlash('danger','Une erreur c\'est produite merci de réessayer ultérieurement !');
        return $this->redirectToRoute('user_workshop_stock');
    }

    /**
     * modif lot
     * @Route("/modif/Lots/{id}", name="article_modif_lots")
     * @return void
     */
    public function modifLot(Request $req, EntityManagerInterface $manager, Lots $lots){

            extract($_POST);
            $lotNumber = $_POST['lotNumber'];
            $lotQ = $_POST['lotQuantity'];
            $lotPlace = $_POST['lotPlace'];
            // $lotDateEnter = $_POST['dateEnter'];
            // $lotDateExp = $_POST['dateExp'];
            $articleLot = $lots->getArticle();
            $quantityArticle = $articleLot->getQuantity();
            $lotIntialQuantity = $lots->getQuantity();
            if($lotQ > $lotIntialQuantity){
                $diffLot = $lotQ - $lotIntialQuantity;
                $articleLot->setQuantity($quantityArticle + $diffLot); 
            }elseif($lotQ < $lotIntialQuantity){
                $diffLot = $lotIntialQuantity - $lotQ;
                $articleLot->setQuantity($quantityArticle - $diffLot); 
            }
            $lots->setQuantity($lotQ);
            $lots->setNumber($lotNumber);
            $lots->setPlace($lotPlace);
            // $lots->setDateEnter($lotDateEnter);
            // $lots->setDateExp($lotDateExp);
            $manager->persist($articleLot);
            $manager->persist($lots);
            $manager->flush();
            $this->addFlash("success", "modification éffectuer");
            return $this->redirectToRoute('user_workshop_stock');
    }

    /**
     * delete images
     * 
     * @Route("/delete/image/{id}", name="article_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $req){
        $data = json_decode($req->getContent(), true);
        if($this->isCsrfTokenValid('delete' .$image->getId(), $data['_token'])){
            $name = $image->getName();
            unlink($this->getParameter('images_directory').'/'.$name);
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }
}
