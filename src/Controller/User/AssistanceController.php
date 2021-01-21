<?php

namespace App\Controller\User;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AssistanceController extends AbstractController
{
    /**
     * @Route("/assistance", name="assistance")
     */
    public function index(Request $req, MailerInterface $mailer)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($req);

        $client = $this->getUser();
        $firstname = $client->getFirstname();
        $lastname = $client->getLastname();
        $phone = $client->getPhone();
        $mail = $client->getEmail();
        $messageContact = $contact->getMessage();

        if($form->isSubmitted() && $form->isValid()){

            $attachment = $form->get('attachment')->getData();
            foreach($attachment as $attach){
                $fichier = md5(uniqid()) . '.' . $attach->guessExtension();
                $attach->move(
                    $this->getParameter('attachment_directory'),
                    $fichier
                );
            }

                $message = (new TemplatedEmail())
                ->from($mail)
                ->to('wdelvaux@idtpe.fr')
                ->subject('Demande client de son espace utilisateur')
                ->html("<p>
                Nom: $lastname<br>
                Prenom: $firstname<br>
                Email: $mail<br>
                Téléphone: $phone<br>
                Message: $messageContact
                </p>")
                ->attachFromPath($this->getParameter('attachment_directory') . '/' . $fichier);
    
                $mailer->send($message);

                $message = (new TemplatedEmail())
                ->from($mail)
                ->to('wdelvaux@idtpe.fr')
                ->subject('Demande client de son espace utilisateur')
                ->html("<p>
                Nom: $lastname<br>
                Prenom: $firstname<br>
                Email: $mail<br>
                Téléphone: $phone<br>
                Message: $messageContact
                </p>");
    
                $mailer->send($message);

            $message2 = (new TemplatedEmail())
            ->from($mail)
            ->to('gbirlouez@idtpe.fr')
            ->subject('Demande client BARRI de son espace utilisateur')
            ->html("<p>
                        Nom: $lastname<br>
                        Prenom: $firstname<br>
                        Email: $mail<br>
                        Téléphone: $phone<br>
                        Message: $messageContact
                        </p>")
            ->attachFromPath($this->getParameter('attachment_directory') . '/' . $fichier);
            $mailer->send($message2);

            $message3 = (new TemplatedEmail())
            ->from('assistance@barri.fr')
            ->to($mail)
            ->subject('Copie de votre message sur votre compte BARRI')
            ->html("<p>
                        Votre message:<br> $messageContact
                        <br>
                        <br>
                        <strong>
                            A l’écoute permanente des demandes utilisateurs nous vous remercions  pour votre message auquel nous allons prêter  une grande attention. Nous mettons tout en œuvre pour vous répondre dans les meilleurs délais
                        </strong>
                        <br>
                        <br>
                        (ce message est automatique, merci de ne pas y répondre.)
                        </p>")
            ->attachFromPath($this->getParameter('attachment_directory') . '/' . $fichier);
            $mailer->send($message3);

            unlink($this->getParameter('attachment_directory').'/'.$fichier);

            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute("assistance");
            }
        return $this->render('assistance/index.html.twig', [
            'controller_name' => 'AssistanceController',
            'form' => $form->createView(),
            
        ]);
    }
}