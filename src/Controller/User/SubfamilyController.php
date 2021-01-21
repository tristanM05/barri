<?php

namespace App\Controller\User;

use App\Entity\Subfamily;
use App\Form\SubfamilyType;
use App\Repository\SubfamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubfamilyController extends AbstractController
{
    private $secu;


    /**
     * Allows a customer to see all his subfamilies
     * 
     * @Route("/user/subfamilies", name="user_subfamilies")
     */
    public function allSubfamilies(SubfamilyRepository $repo, EntityManagerInterface $manager){
        $client = $this->getUser();

        $client->setLast_action(new \DateTime("now"));
        $manager->persist($client);
        $manager->flush();

        $subfamilies = $repo->findBy(['client' => $client]);
        return $this->render('User/subfamily/all_subfamilies.html.twig',[
            'subfamilies' => $subfamilies
        ]);
    }

    /**
     * Allows a customer to edit or add one of his subfamilies
     * 
    * @Route("/user/subfamily/add", name="user_subfamily_add")
    * @Route("/user/subfamily/edit/{id}", name="user_subfamily_edit")
    */
    public function editAndAddSubfamily(Subfamily $subfamily = null, Request $request ,EntityManagerInterface $manager){

        // Check if the client is premium
            if (!$this->getUser()->getIsPremium()) {
                $this->addFlash('warning','Vous ne pouvez pas accèder a cette fonctionnalité car votre abonnement n\' a pas été renouvelé');
                return $this->redirectToRoute('user_articles');
            }
        // End check if the client is premium

        $user = $this->getUser()->getId();

        if(!$subfamily){
            $subfamily = new Subfamily();
            $subfamily->setClient($this->getUser());
        }

        $form = $this->createForm(SubfamilyType::class, $subfamily, ['client' => $user]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $edit = $subfamily->getId() !== null;

            $manager->persist($subfamily);
            $manager->flush();

            $this->addFlash("success",($edit) ? "La catégorie à bien été modifiée" : "L'ajout de la catégorie à bien été effectuée");
            return $this->redirectToRoute('user_subfamilies');

        }

         return $this->render('User/subfamily/edit_&_add_subfamily.html.twig', [
             'form_subfamily' => $form->createView(),
             'subfamily' => $subfamily,
             'isEdit' => $subfamily->getId() != null
         ]);
    }

    /**
     * Allows a customer to delete one of his subfamilies
     * 
    * @Route("/user/subfamily/delete/{id}", name="user_subfamily_delete")
    */
    public function deleteSubfamily(Subfamily $subfamily, Request $request ,EntityManagerInterface $manager){

        // Check if the client is premium
            if (!$this->getUser()->getIsPremium()) {
                $this->addFlash('warning','Vous ne pouvez pas accèder a cette fonctionnalité car votre abonnement n\' a pas été renouvelé');
                return $this->redirectToRoute('user_articles');
            }
        // End check if the client is premium

        if ($this->isCsrfTokenValid("SUP".$subfamily->getId(),$request->get('_token'))) {
            $manager->remove($subfamily);
            $manager->flush();
            return $this->redirectToRoute('user_subfamilies');
        }
    }
}
