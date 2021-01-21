<?php

namespace App\Controller\User;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\DashboardUserService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlertController extends AbstractController
{
    /**
     * affiche les produit possédant une date d'alerte pour l'expiration
     * @Route("/user/alert", name="alert")
     */
    public function alert1(DashboardUserService $service, ArticleRepository $repo)
    {
        $now = new \DateTime('now');
        $now->setTime(0,0,0);
        $client = $this->getUser()->getId();
        $email = $this->getUser();
        $article = $repo->findBy(['client' => $client, 'dateLimit' => $now, 'productStatus' => 1]);

        return $this->render('alert/index.html.twig', [
            'article' => $article,
            // 'interval' => $interval,
            'email' => $email->getEmail()
        ]);
    }

    // /**
    //  * affiche les produit dont la date d'expiration est dépasser
    //  * @Route("/user/alert2", name="alert2")
    //  * @return void
    //  */
    // public function alert2(ArticleRepository $repo){
    //     $now = new \DateTime('now');
    //     $client = $this->getUser()->getId();
    //     $email = $this->getUser();
    //     $article = $repo->findBy(['client' => $client, 'endDate' => $now]);

    //     return $this->render('alert/index2.html.twig', [
    //         'article' => $article,
    //         'email' => $email->getEmail()
    //     ]);      
    // }

    /**
     * répertorie tout les article dont la date d'expiration est expiré
     * @Route("/user/expired", name="expiredDate")
     * @return void
     */
    public function expired(DashboardUserService $service){
        $now = new \DateTime('now');
        $now->setTime(0,0,0);
        $client = $this->getUser()->getId();
        $article = $service->getExpired($client, $now);

        return $this->render('alert/expired.html.twig', [
            'article' => $article
        ]);
    }

    /**
    * @Route("/user/exportPDF/alert1", name="user_PDF_alert1")
    */
    public function sendDatasAlert1(MailerInterface $mailer, DashboardUserService $service){
        extract($_POST);

        $user = $this->getUser()->getId();
        $client = $this->getUser();
        $date = new \DateTime('now');
        $date->setTime(0,0,0);

        $article = $service->getAlert1($user, $date);

        foreach($article as $articles){
            $limit = $articles->getEndDate();
        }
        $diff = $date->diff($limit);
        $interval = $diff->format('%a jours');

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        
        $html = $this->renderView('alert/alert1_PDF.html.twig', [
            'articles' => $article,
            'date' => $date,
            'interval' => $interval
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $output = $dompdf->output();
        
        file_put_contents('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf', $output);

            $message = (new TemplatedEmail())
                ->from('barri@barri.fr')
                ->to($email)
                ->subject('Tableau de produit')
                ->htmlTemplate('User/workshop_stock/partials/_mail_all_articles.html.twig')
                ->attachFromPath('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf');
            
            $mailer->send($message);

            unlink('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf');

        $this->addFlash("success","L'email à été envoyé avec succès");

        return $this->redirectToRoute('alert');
    }

    /**
    * @Route("/user/exportPDF/alert2", name="user_PDF_alert2")
    */
    public function sendDatasAlert2(MailerInterface $mailer, ArticleRepository $repo){
        extract($_POST);

        $user = $this->getUser()->getId();
        $client = $this->getUser();
        $date = new \DateTime('now');
        $article = $repo->findBy(['client' => $client, 'endDate' => $date,  'productStatus' => 1]);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        
        $html = $this->renderView('alert/alert2_PDF.html.twig', [
            'articles' => $article,
            'date' => $date
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $output = $dompdf->output();
        
        file_put_contents('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf', $output);

            $message = (new TemplatedEmail())
                ->from('barri@barri.fr')
                ->to($email)
                ->subject('Tableau de produit')
                ->htmlTemplate('User/workshop_stock/partials/_mail_all_articles.html.twig')
                ->attachFromPath('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf');
            
            $mailer->send($message);

            unlink('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf');

        $this->addFlash("success","L'email à été envoyé avec succès");

        return $this->redirectToRoute('alert2');
    }

}
