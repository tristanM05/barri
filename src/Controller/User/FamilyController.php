<?php

namespace App\Controller\User;

use App\Entity\Family;
use App\Form\FamilyType;
use App\Repository\FamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FamilyController extends AbstractController
{
    /**
     * Allows a customer to see all his families
     * 
     * @Route("/user/families", name="user_families")
     */
    public function allFamilies(FamilyRepository $repo, EntityManagerInterface $manager){
        $client = $this->getUser();

        $client->setLast_action(new \DateTime("now"));
        $manager->persist($client);
        $manager->flush();

        $families = $repo->findBy(['client' => $client]);
        return $this->render('User/family/all_families.html.twig', [
            'families' => $families,
        ]);
    }

    /**
     * Allows a customer to edit or add one of his families
     * 
    * @Route("/user/family/add", name="user_family_add")
    * @Route("/user/family/edit/{id}", name="user_family_edit")
    */
    public function editAndAddFamily(Family $family = null, Request $request, EntityManagerInterface $manager){
        // Check if the client is premium
            if (!$this->getUser()->getIsPremium()) {
                $this->addFlash('warning','Vous ne pouvez pas accèder a cette fonctionnalité car votre abonnement n\' a pas été renouvelé');
                return $this->redirectToRoute('user_articles');
            }
        // End check if the client is premium

        if (!$family) {
            $family = new Family();
            $family->setClient($this->getUser());
        }

        $form = $this->createForm(FamilyType::class, $family);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $edit = $family->getId() !== null;
            $manager->persist($family);
            $manager->flush();

            $this->addFlash("success",($edit) ? "La famille à bien été modifiée" : "L'ajout de la famille à bien été effectuée");
            return $this->redirectToRoute('user_families');
        }

         return $this->render('User/family/edit_&_add_family.html.twig', [
             'form_family' => $form->createView(),
             'family' => $family,
             'isEdit' => $family->getId() !== null
         ]);
    }

    /**
     * Allows a customer to delete one of his families
     * 
    * @Route("/user/family/delete/{id}", name="user_family_delete")
    */
    public function deleteFamily(Family $family, Request $request, EntityManagerInterface $manager){

        // Check if the client is premium
            if (!$this->getUser()->getIsPremium()) {
                $this->addFlash('warning','Vous ne pouvez pas accèder a cette fonctionnalité car votre abonnement n\' a pas été renouvelé');
                return $this->redirectToRoute('user_articles');
            }
        // End check if the client is premium

        if ($this->isCsrfTokenValid("SUP".$family->getId(),$request->get('_token'))) {
            $manager->remove($family);
            $manager->flush();
            return $this->redirectToRoute('user_families');
        }
    }
}
