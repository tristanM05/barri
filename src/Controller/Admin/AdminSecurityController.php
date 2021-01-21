<?php

namespace App\Controller\Admin;

use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminSecurityController extends AbstractController
{
    /**
     * Allows an administrator to log in
     * 
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $util){
        return $this->render('Admin/account/admin_login.html.twig',[
            "lastUserName" => $util->getLastUsername(),
            "error" => $util->getLastAuthenticationError()
        ]);
    }

    /**
     * Allows an administrator to log out
     * 
    * @Route("/admin/logout", name="admin_account_logout")
    */
    public function logout(){
        
    }

    /**
     * @Route("/admin/edit_profile/{id}", name="admin_edit_profile")
     */
    public function editProfile(EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder){
        $client = $this->getUser();

        $passwordUpdate = new PasswordUpdate();

        $formPassword = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(), $client->getPass())) {
                $formPassword->get('oldPassword')->addError(new FormError("Le mot de passe actuel n'est pas correcte"));
            } else {
                $new_password = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($client,$new_password);

                $client->setPass($hash);
                $manager->persist($client);
                $manager->flush();

                $this->addFlash('success', "Votre mot de passe à bien été modifié !");

                return $this->redirectToRoute('admin_edit_profile', array('id' => $client->getId()));

            }
            
        }

        $form = $this->createForm(AccountType::class,$client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($client);
            $manager->flush();

            $this->addFlash('success','Vos informations ont bien été modifiées');
        }


        return $this->render('Security/profil.html.twig', [
            'form' => $form->createView(),
            'formPassword' => $formPassword->createView()
        ]);
    }
}
