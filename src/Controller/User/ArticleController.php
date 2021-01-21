<?php

namespace App\Controller\User;

use DateTime;
use App\Entity\Images;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ImagesRepository;
use App\Repository\SalepointRepository;
use App\Repository\SubfamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductStatusRepository;
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
        // Check if the client is premium
            // if (!$this->getUser()->getIsPremium()) {
            //     $this->addFlash('warning','Vous ne pouvez pas accèder a cette fonctionnalité car votre abonnement n\' a pas été renouvelé');
            //     return $this->redirectToRoute('user_articles');
            // }
        // End check if the client is premium
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
    public function editAndAddArticle(Article $article = null, Request $request, EntityManagerInterface $manager, SubfamilyRepository $repo_subfamily,SalepointRepository $repo_salepoint, ProductStatusRepository $repo_product_status){
        
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

            $manager->persist($article);
            $manager->flush();

            $this->addFlash("success",($edit) ? "L'article à bien été modifié" : "L'article à bien été ajouté");
            return $this->redirectToRoute('user_workshop_stock');
        }

        return $this->render('User/workshop_stock/edit_&_add_article.html.twig', [
            'article' => $article,
            'date' => $date,
            'form_article' => $form->createView(),
            'isEdit' => $article->getId() !== null,
            'isDuplicate' => null,
        ]);
    }

    /**
    * @Route("/user/article/duplicate/{id}", name="user_duplicate_article")
    */
    public function articleDuplication(Article $article, Request $request, EntityManagerInterface $manager){

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
        
        return $this->render('User/workshop_stock/edit_&_add_article.html.twig', [
            'article' => $new_article,
            'date' => $date,
            'form_article' => $form->createView(),
            'isEdit' => $new_article->getId() !== null,
            'isDuplicate' => true
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
