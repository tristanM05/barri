<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/user/edit_profile/{id}", name="user_edit_profile")
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

                return $this->redirectToRoute('user_edit_profile', array('id' => $client->getId()));

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

    /**
     * @Route("/user/subscription", name="user_subscription")
     */
    public function clientSubscription(){

        return $this->render('User/subscription/subscription_user.html.twig', []);
    }

}
