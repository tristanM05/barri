<?php

namespace App\Controller\User;

use App\Entity\Salepoint;
use App\Form\SalepointType;
use App\Repository\SalepointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SalepointController extends AbstractController
{
    /**
     * Allows a customer to see all his salepoints
     * 
     * @Route("/user/salepoints", name="user_salepoints")
     */
    public function allSalepoints(SalepointRepository $salepointrepo, EntityManagerInterface $manager){
        $client = $this->getUser();

        $client->setLast_action(new \DateTime("now"));
        $manager->persist($client);
        $manager->flush();

        $salepoints = $salepointrepo->findBy(['client' => $client]);

        return $this->render('User/salepoint/all_salepoints.html.twig', [
            'salepoints' => $salepoints,
        ]);
    }

    /**
     * Allows a customer to edit or add one of his salepoints
     * 
    * @Route("/user/salepoint/add", name="user_salepoint_add")
    * @Route("/user/salepoint/edit/{id}", name="user_salepoint_edit")
    */
    public function editAndAddSalepoint(Salepoint $salepoint = null, Request $request, EntityManagerInterface $manager){

        // Check if the client is premium
            if (!$this->getUser()->getIsPremium()) {
                $this->addFlash('warning','Vous ne pouvez pas accèder a cette fonctionnalité car votre abonnement n\' a pas été renouvelé');
                return $this->redirectToRoute('user_salepoints');
            }
        // End check if the client is premium
        
        if (!$salepoint) {
            $salepoint = new Salepoint();
            $salepoint->setClient($this->getUser());
        }

        $form = $this->createForm(SalepointType::class, $salepoint);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $edit = $salepoint->getId() !== null;
            
            $manager->persist($salepoint);
            $manager->flush();
            $this->addFlash("success",($edit) ? "La modification du point de vente à bien été effectuée" : "L'ajout du point de vente à bien été effectuée");

            return $this->redirectToRoute('user_salepoints');
        }

        return $this->render('User/salepoint/edit_&_add_salepoint.html.twig', [
            'salepoint' => $salepoint,
            'form_salepoint' => $form->createView(),
            'isEdit' => $salepoint->getId() !== null
        ]);
        
         
    }

    /**
     * Allows a customer to delete one of his families
     * 
    * @Route("/user/salepoint/delete/{id}", name="user_salepoint_delete")
    */
    public function deleteSalepoint(Salepoint $salepoint, Request $request, EntityManagerInterface $manager){

        // Check if the client is premium
            if (!$this->getUser()->getIsPremium()) {
                $this->addFlash('warning','Vous ne pouvez pas accèder a cette fonctionnalité car votre abonnement n\' a pas été renouvelé');
                return $this->redirectToRoute('user_salepoints');
            }
        // End check if the client is premium

        if ($this->isCsrfTokenValid("SUP".$salepoint->getId(),$request->get('_token'))) {
            $manager->remove($salepoint);
            $manager->flush();
            return $this->redirectToRoute('user_salepoints');
        }
        
    }
}
