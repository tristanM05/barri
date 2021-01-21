<?php

namespace App\Controller\User;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController{


    /**
     * @Route("/", name="user_home")
     */
    public function userIndex(AuthenticationUtils $util ){
        if ($this->getUser()) {
            if ($this->getUser()->getActivationToken()) {
                $this->addFlash('danger','Votre compte n\' a pas été valider');
                return $this->redirectToRoute('account_login');
                // return $this->render('Security/login.html.twig',[
                //     "lastUserName" => $util->getLastUsername(),
                //     "error" => $util->getLastAuthenticationError()
                // ]);
            }else{
                
                return $this->render('User/index.v2.html.twig', []);
            }
        }else{
            // $this->addFlash('danger','Vous n\'êtes pas connecté');
            return $this->redirectToRoute('account_login');
            // return $this->render('Security/login.html.twig',[
            //     "lastUserName" => $util->getLastUsername(),
            //     "error" => $util->getLastAuthenticationError()
            // ]);
        }

    }

    /**
     * @Route("/legal_notices", name="legal_notices")
     */
    public function legalNotices(){
        return $this->render('Footer/legal_notices.html.twig', []);
    }




}
