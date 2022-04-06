<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Family;
use App\Entity\Article;
use App\Entity\Journal;
use App\Entity\Salepoint;
use App\Entity\Subfamily;
use App\Form\ResetPassType;
use App\Entity\ProductStatus;
use App\Form\RegistrationType;
use App\Entity\UserConfiguration;
use App\Repository\ButtonRepository;
use App\Repository\ClientRepository;
use App\Repository\FamilyRepository;
use App\Repository\SalepointRepository;
use App\Repository\SubfamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductStatusRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{

    /**
     * Client login page
     * 
     * 
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $util, Request $req, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer, ButtonRepository $repoButton){

        $maintenance = $repoButton->findOneBy(['name' => 'maintenance']);

        $error = $util->getLastAuthenticationError();
        $last_username = $util->getLastUsername();
        $client = new Client();

        $form = $this->createForm(RegistrationType::class, $client,[
            'action' => $this->generateUrl('account_register'),
        ]);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordHash = $encoder->encodePassword($client,$client->getPassword());
            $client->setPass($passwordHash);

            // activation token generation
                // Crypt an uniqid in md5
            $client->setActivationToken(md5(uniqid()));
            $client->setRoles("ROLE_NULL");
            $client->setLast_action(new \DateTime("now"));

            $manager->persist($client);
            $manager->flush();

            // Create the message
            $message = (new TemplatedEmail())
                // Sender
                ->from('barri@barri.fr')
                // Recipient
                ->to($client->getEmail())
                // Subject
                ->subject('Activation de votre compte Barri')
                // Content
                ->htmlTemplate('Security/mail_activation.html.twig')
                // Variable
                ->context([
                    'token' => $client->getActivationToken()
                ]);
            
            // Send the mail
            $mailer->send($message);

            // Send a message
            $this->addFlash('success', 'Un mail vient de vous être envoyé afin de valider votre compte');

            return $this->redirectToRoute("account_login");
        }

        return $this->render('Security/login.html.twig',[
            "lastUserName" => $last_username,
            "error" => $error,
            "form" => $form->createView(),
            'button' => $maintenance
        ]);
    }

    /**
     * Client registration page
     * 
    * @Route("/register", name="account_register")
    */
    public function register(Request $req, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer){

        $client = new Client();

        $form = $this->createForm(RegistrationType::class, $client);
        $form->handleRequest($req);


        if ($form->isSubmitted() && $form->isValid()) {
            $passwordHash = $encoder->encodePassword($client,$client->getPassword());
            $client->setPass($passwordHash);

            // activation token generation
                // Crypt an uniqid in md5
            $client->setActivationToken(md5(uniqid()));
            $client->setRoles("ROLE_NULL");
            $client->setLast_action(new \DateTime("now"));

            $manager->persist($client);
            $manager->flush();

            $lastName = $client->getLastname();
            $firstName = $client->getFirstname();
            $clientMail = $client->getEmail();

            // Create the message
            $message = (new TemplatedEmail())
                // Sender
                ->from('barri@barri.fr')
                // Recipient
                ->to($client->getEmail())
                // Subject
                ->subject('Activation de votre compte Barri')
                // Content
                ->htmlTemplate('Security/mail_activation.html.twig')
                // Variable
                ->context([
                    'token' => $client->getActivationToken()
                ]);
            
            // Send the mail
            $mailer->send($message);

               // Create the message
            $message2 = (new TemplatedEmail())
            // Sender
            ->from('barri@barri.fr')
            // Recipient
            ->to('wdelvaux@idtpe.fr')
            // Subject
            ->subject('nouvelle inscription')
            // Content
            ->html("<p>Nouvelle inscrption sur Barri.fr !<br>
                        Nom: $lastName<br>
                        Prenom: $firstName<br>
                        Email: $clientMail</p>");
        
            // Send the mail
            $mailer->send($message2);

            
            // Create the message
            $message3 = (new TemplatedEmail())
            // Sender
            ->from('barri@barri.fr')
            // Recipient
            ->to('gbirlouez@idtpe.fr')
            // Subject
            ->subject('nouvelle inscription')
            // Content
            ->html("<p>Nouvelle inscrption sur Barri.fr !<br>
                        Nom: $lastName<br>
                        Prenom: $firstName<br>
                        Email: $clientMail</p>");
        
            // Send the mail
            $mailer->send($message3);

            // Send a message
            $this->addFlash('success', 
            'Bienvenue;
            Vous venez de créer votre espace pour accéder aux fonctionnalités de BARRI,  assistant digital de gestion.
            Un mail d’activation de votre compte vient de vous êtes envoyé ; il vous permettra de découvrir gratuitement l’ergonomie et les principales fonctionnalités de cet outil.
            Si vous n’avez pas reçu le mail d’activation, vérifiez qu’il ne figure pas dans vos spams. 
            N’hésitez pas à contacter notre équipe si vous rencontrez une quelconque difficulté. L’assistance personnalisée est au centre de toutes nos actions.
            sdurin@idtpe.fr 07 66 58 02 67
            '
        );

            return $this->redirectToRoute("account_login");
        }

        $this->addFlash('danger', 'Une erreur est survenue merci de réessayer ultérieurement');

        return $this->redirectToRoute("account_login");
    }

    /**
     * Allows you to activate the client's account
     * 
    * @Route("/activation/{token}", name="account_activation")
    */
    public function activation($token, ClientRepository $repo, FamilyRepository $repo_family, ProductStatusRepository $repo_status,  SubfamilyRepository $repo_subfamily,SalepointRepository $repo_salepoint){

        // we're checking to see if a user at this token
        $client = $repo->findOneBy(['activation_token' => $token]);

        // if no user at this token
        if(!$client){
            // Error 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // Delete the token
        $client->setActivationToken(null);
        $client->setRoles("ROLE_USER");
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($client);
        $entityManager->flush();


        // Create the default salepoint
        $salepoint_Atelier = new Salepoint();
        $salepoint_Atelier->setWording("Atelier")
                          ->setClient($client)
                          ->setVisible(1);
        $entityManager->persist($salepoint_Atelier);
        $entityManager->flush();

        // Create the default family
        $family = new Family();
        $family->setWording("Indéfini")
               ->setClient($client);
        $entityManager->persist($family);
        $entityManager->flush();

        // Create & associate the default subfamily with the default family
        $find_family = $repo_family->findOneBy(['client'=> $client]);
        $subfamily = new Subfamily();
        $subfamily->setWording("Indéfini")
                  ->setFamily($find_family)
                  ->setClient($client);
        $entityManager->persist($subfamily);
        $entityManager->flush();

        // Create the default user configuration(management page)
        $user_configuration = new UserConfiguration();
        $user_configuration->setMaxsupertop(1)
                           ->setMaxtop(6)
                           ->setClient($client);
        $entityManager->persist($user_configuration);

        //Create the demo articles (workshopStock)
        $product_status = $repo_status->findOneBy(['wording' => 'en vente']);
        $sub = $repo_subfamily->findOneBy(['client' => $client]);
        $sale = $repo_salepoint->findOneBy(['client' => $client]);
        $now = new \DateTime('now');
        $now->setTime(0,0,0);

        $demoArticle = new Article();
        $demoArticle->setNumber(001)
                    ->setDesignation('Produit de démonstration')
                    ->setClient($client)
                    ->setReferenceprice(15)
                    ->setProductiondate($now)
                    ->setSalepoint($sale)
                    ->setProductStatus($product_status)
                    ->setSubfamily($sub)
                    ->setIsvisible(1)
                    ->setQuantity(1)
                    ->setTotalPrice(15*1);
        $entityManager->persist($demoArticle);
        $demoArticle2 = new Article();
        $demoArticle2->setNumber(002)
                    ->setDesignation('Produit de démonstration')
                    ->setClient($client)
                    ->setReferenceprice(15)
                    ->setProductiondate($now)
                    ->setSalepoint($sale)
                    ->setProductStatus($product_status)
                    ->setSubfamily($sub)
                    ->setIsvisible(1)
                    ->setQuantity(1)
                    ->setTotalPrice(15*1);
        $entityManager->persist($demoArticle2);
        $demoArticle3 = new Article();
        $demoArticle3->setNumber(003)
                    ->setDesignation('Produit de démonstration')
                    ->setClient($client)
                    ->setReferenceprice(15)
                    ->setProductiondate($now)
                    ->setSalepoint($sale)
                    ->setProductStatus($product_status)
                    ->setSubfamily($sub)
                    ->setIsvisible(1)
                    ->setQuantity(1)
                    ->setTotalPrice(15*1);
        $entityManager->persist($demoArticle3);
        $demoArticle4 = new Article();
        $demoArticle4->setNumber(004)
                    ->setDesignation('Produit de démonstration')
                    ->setClient($client)
                    ->setReferenceprice(15)
                    ->setProductiondate($now)
                    ->setSalepoint($sale)
                    ->setProductStatus($product_status)
                    ->setSubfamily($sub)
                    ->setIsvisible(1)
                    ->setQuantity(1)
                    ->setTotalPrice(15*1);
        $entityManager->persist($demoArticle4);
        $demoArticle5 = new Article();
        $demoArticle5->setNumber(005)
                    ->setDesignation('Produit de démonstration')
                    ->setClient($client)
                    ->setReferenceprice(15)
                    ->setProductiondate($now)
                    ->setSalepoint($sale)
                    ->setProductStatus($product_status)
                    ->setSubfamily($sub)
                    ->setIsvisible(1)
                    ->setQuantity(1)
                    ->setTotalPrice(15*1);
        $entityManager->persist($demoArticle5);
        $entityManager->flush();

        //create new journal
        $journal = new Journal();
        $journal->setDate($now);
        $journal->setClient($client);
        $journal->setTotalStart(0.00);
        $journal->setTotalEspStart(0.00);
        $journal->setTotalCb(0.00);
        $journal->setTotalEsp(0.00);
        $journal->setTotalChq(0.00);
        $journal->setTotalOther(0.00);
        $entityManager->persist($journal);
        $entityManager->flush();


        
        // Send a message
        $this->addFlash('success', 'Votre compte est activé, vous pouvez maintenant vous connecter');
        
        // Redirection 
        return $this->redirectToRoute('account_login');
    }

    /**
     * Allows the client's password to be reset
     * 
    * @Route("/forgotten-password", name="account_forgotten_password")
    */
    public function forgottenPassword(Request $req, ClientRepository $repo, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator){
        // Create the form
        $form = $this->createForm(ResetPassType::class);

        // Process the form
        $form->handleRequest($req);

        // Check the form
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $data = $form->getData();

            // Check if an user have this email
            $client = $repo->findOneByEmail($data['email']);

            // If not user
            if (!$client) {
                // Send message
                $this->addFlash('danger', 'Aucun compte n\'est associé à cet email');
                return $this->redirectToRoute('account_forgotten_password');
            }

            // Generate a token
            $token = $tokenGenerator->generateToken();

            try{
                $client->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($client);
                $entityManager->flush();
            }catch(\Exception $e){
                $this->addFlash('warning', 'Une erreur est survenue :' .$e->getMessage());
                return $this->redirectToRoute('account_forgotten_password');
            }


            // Create the message
            $message = (new TemplatedEmail())
                // Sender
                ->from('barri@barri.fr')
                // Recipient
                ->to($client->getEmail())
                // Subject
                ->subject('Réinitialisation du mot de passe')
                // Content
                ->htmlTemplate('Security/mail_reset_password.html.twig')
                // Variable
                ->context([
                    'token' => $client->getResetToken()
                ]);
            
            // Send the mail
            $mailer->send($message);

            // Create flash message
            $this->addFlash('success', 'Un e-mail de réinitialisation de votre mot de passe vient de vous être envoyé sur votre adresse mail');

            return $this->redirectToRoute('account_login');
        }
         return $this->render('Security/forgotten_password.html.twig', [
             'emailForm' => $form->createView()
         ]);
    }

    /**
     * Resets the client's password using the client's token.
     * 
    * @Route("/reset-password/{token}", name="account_reset_password")
    */
    public function resetPassword($token, Request $req, UserPasswordEncoderInterface $encoder){

        var_dump($req->request->get('password'));
        // Search user with the TokenGenerator
        $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['reset_token' => $token]);

        if(!$client){
            $this->addFlash('danger','Token inconnu');
            return $this->redirectToRoute('account_login');
        }

        // If form is send in POST
        if($req->isMethod('POST')){
            $length_password = strlen($req->request->get('password'));

            if($length_password < 5 && $length_password > 31){
                $this->addFlash('danger', 'Le mot de passe doit faire minimum 6 caractères et maximum 30 caractères');
                return $this->render('Security/reset_password.html.twig', [
                'token' => $token
                ]);
            }

            // Delete the Token
            $client->setResetToken(null);

            // Encrypt the new $password
            $client->setPass($encoder->encodePassword($client, $req->request->get('password')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès');
            return $this->redirectToRoute('account_login');
            
            
        }else{
            return $this->render('Security/reset_password.html.twig', [
                'token' => $token
            ]);
        }
    }

    /**
     * Allow the client's logout
     * 
    * @Route("/logout", name="account_logout")
    */
    public function logout(EntityManagerInterface $manager){
        $client = $this->getUser()->setIsconnected(false);
        $manager->persist($client);
        $manager->flush();

    }

    /**
     * @Route("/concent", name="pdf_view")
     */
    public function pdf()
    {
        $baseUrl = 'https://www.barri.fr/public';
        $url = $baseUrl.'/pdf/concent.pdf';
        return $this->redirect($url);
    }

}
