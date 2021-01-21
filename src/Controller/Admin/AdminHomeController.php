<?php

namespace App\Controller\Admin;

use App\Entity\Button;
use App\Entity\Client;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Entity\ContactAdmin;
use App\Form\ContactAdminType;
use App\Form\FilterClientType;
use App\Repository\ButtonRepository;
use App\Repository\ClientRepository;
use App\Service\FilterClientService;
use App\Service\DashboardAdminService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminHomeController extends AbstractController
{
    /**
     * Admin dashboard
     * 
     * @Route("/admin", name="admin_home")
     */
    public function index(DashboardAdminService $serviceAdmin, Request $request, ClientRepository $repo, ButtonRepository $repoButton){

        $button = $repoButton->findAll();
        $dateNow = new \DateTime("now");
        $infoClient = $repo->findAll();

        $countUsers = $serviceAdmin->getCountUsers();
        $countItems = $serviceAdmin->getCountItems();
        $countStock = $serviceAdmin->getCountStock();
        $countConnectedUsers = $serviceAdmin->getCountConnectedUSers($dateNow);

        $data = new FilterClientService();
        $data->page = $request->get('page',1);
        $form = $this->createForm(FilterClientType::class, $data);
        $form->handleRequest($request);
        $clients = $repo->findSearch($data);
        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('Admin/_clients.html.twig', ['clients' => $clients]),
                'sorting' => $this->renderView('Admin/_sorting.html.twig', ['clients' => $clients]),
                'pagination' => $this->renderView('Admin/_pagination.html.twig', ['clients' => $clients]),
                'pages' => ceil($clients->getTotalItemCount() / $clients->getItemNumberPerPage())
            ]);
        }

        return $this->render('Admin/index.html.twig', [
            'countStock' => $countStock,
            'countusers' => $countUsers,
            'countitems' => $countItems,
            'countconnectedusers' => $countConnectedUsers,
            'clients' => $clients,
            'form' => $form->createView(),
            'button' => $button,
            'infoClient' => $infoClient
        ]);
    }

    /**
     * Allows an administrator to change the status of a client
     *
     * @Route("/admin/client/{id}", name="admin_premium_client")
     * @param Client $client
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function premium(Client $client, EntityManagerInterface $manager) : Response {

        if($client->getIsPremium() == true){
            // $user = $repo->findOneBy(['id' => $client]);
            $client->setIsPremium(false);

            $manager->persist($client);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => "Statut de l'utilisateur changé",
                'premium' => '<i class="fas fa-check text-success fa-2x"></i>'
            ],200);
        }

        $client->setIsPremium(true);
        $manager->persist($client);
        $manager->flush();

        return $this->json(['code' => 200, 'message' => "Statut de l'utilisateur changé V.2", 'premium' => '<i class="fas fa-check text-success fa-2x"></i>'],200);
    }

    /**
     * maintenance
     * 
     * @Route("/admin/maintenance", name="maintenance")
     */
    public function maintenanceMode(EntityManagerInterface $manager, ButtonRepository $repo, ClientRepository $repoClient){
            $maintenance = $repo->findOneBy(['name' => 'maintenance'] );
            $client = $repoClient->findBy(['roles' => 'ROLE_USER']);
            
            if($client){
                session_destroy();
            }
            $maintenance->setStatus(1);
            $manager->persist($maintenance);
            $manager->flush();
            return $this->redirectToRoute("admin_home");

    }
    /**
     * maintenance
     * 
     * @Route("/admin/maintenanceStop", name="maintenanceStop")
     */
    public function maintenanceModeStop(EntityManagerInterface $manager, ButtonRepository $repo){
        $maintenance = $repo->findOneBy(['name' => 'maintenance'] );
        
        $maintenance->setStatus(0);
        $manager->persist($maintenance);
        $manager->flush();
        return $this->redirectToRoute("admin_home");

    }

    /**
     * dele user
     * @Route("/admin/delete/{id}", name="supClient", methods="delete")
     *
     * @param Client $client
     * @param EntityManagerInterface $manager
     * @param Request $req
     * @return void
     */
    public function delete(Client $client, EntityManagerInterface $manager, Request $req){
        if($this->isCsrfTokenValid('SUP' . $client->getId(), $req->get('_token'))){
            $manager->remove($client);
            $manager->flush();
            $this->addFlash("success", "La supression a bien été éffectuée");
            return $this->redirectToRoute("admin_home");
        }
    }
    
    /**
     * info client
     *  @Route("/admin/info/{id}", name="infoClient")
     */
    public function infoClient(Client $client, Request $req, MailerInterface $mailer, DashboardAdminService $countArticle){
        
        $contact = new ContactAdmin();
        $form = $this->createForm(ContactAdminType::class, $contact);
        $form->handleRequest($req);

        $user = $this->getUser();
        $receiver = $client->getEmail();
        $id = $client->getId();
        $mail = $user->getEmail();
        $messageContact = $contact->getMessage();

        $count = $countArticle->getCountItemByUser($id);
        $value = $countArticle->getValueStock($id);

        if($form->isSubmitted() && $form->isValid()){


            $message = (new TemplatedEmail())
            ->from($mail)
            ->to($receiver)
            ->subject('message de l\'administrateur')
            ->html("<p>
                Message: $messageContact
            </p>");

            $mailer->send($message);

            $this->addFlash('success', 'Votre message a bien été envoyé');
                return $this->redirectToRoute("admin_home");
        }

        return $this->render('Admin/client/infoClient.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
            'count' => $count,
            'value' => $value,
        ]);
    }
}
