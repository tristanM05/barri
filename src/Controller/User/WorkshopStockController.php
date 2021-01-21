<?php

namespace App\Controller\User;

use App\Entity\Article;
use App\Entity\Client;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Form\FilterArticleType;
use App\Repository\ArticleRepository;
use App\Repository\ClientRepository;
use App\Repository\StockClientRepository;
use App\Service\DashboardUserService;
use App\Service\FilterArticleService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WorkshopStockController extends AbstractController{


    /**
     * @Route("/user/workshop_stock",name="user_workshop_stock")
    */
    public function workshopStock(ArticleRepository $repo, EntityManagerInterface $manager, Request $request, DashboardUserService $service_request, StockClientRepository $repoGraph){
        $user = $this->getUser()->getId();
        $client = $this->getUser();

        $now = new \DateTime('now');
        $now->setTime(0,0,0);
        $expired = $service_request->getExpired($client, $now);

        $client->setLast_action(new \DateTime("now"));
        $manager->persist($client);
        $manager->flush();

        $stockCount = $service_request->getStockAndValue($user);
        $storageLifeAvg = $service_request->getStorageLifeAvg($user);

        $data = new FilterArticleService();
        $data->page = $request->get('page',1);
        $form = $this->createForm(FilterArticleType::class, $data, ['client' => $client]);
        $form->handleRequest($request);
        $articles = $repo->findSearch($data, $client);

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('User/workshop_stock/partials/_articles.html.twig', ['articles' => $articles]),
                'sorting' => $this->renderView('User/workshop_stock/partials/_sorting.html.twig', ['articles' => $articles]),
                'pagination' => $this->renderView('User/workshop_stock/partials/_pagination.html.twig', ['articles' => $articles]),
                'pages' => ceil($articles->getTotalItemCount() / $articles->getItemNumberPerPage())
            ]);
        }
        $graphs1 = $repoGraph->findBy(["client" => $user], ["date" => "DESC"], (15));
        $graphs = array_reverse($graphs1);
        $date = [];
        $valueStock = [];
        $nbStock = [];

        foreach($graphs as $graph){
            $date[] = $graph->getDate()->format('d-m');
            $valueStock[] = $graph->getValue();
            $nbStock[] = $graph->getQuantity();
        }

        $now = new \DateTime('now');
        $alert = $repo->findOneBy(['client' => $client, 'dateLimit' => $now]);
        $alert2 = $repo->findBy(['client' => $client, 'endDate' => $now]);

        return $this->render('User/workshop_stock/workshop_stock.html.twig', [
            // NUMBER STOCK AND VALUE AT A DATE
            'date' => json_encode($date),
            'value' => json_encode($valueStock),
            'quantity' => json_encode($nbStock),
            'stockCount' => $stockCount,
            'articles' => $articles,
            'avgstock' => $storageLifeAvg, 
            'email' => $client->getEmail(),
            'form' => $form->createView(),
            'now' => $now,
            'alert' => $alert,
            'alert2' => $alert2,
            'expired' =>$expired
        ]);
    }

    /**
     * @Route("/user/exportCSV", name="user_export_articles_in_CSV")
     */
    public function ExportArticlesInCSV(DashboardUserService $service){
        $user = $this->getUser()->getId();

        $results = $service->getAllArticlesASC($user);

        $spreadsheet = new Spreadsheet();
        
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $i = 1;
        $b = 'A';
        foreach ($results as $result){
            $i++;
            $b++;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'Numéro');
            $sheet->setCellValue('B'.$i, $result->getNumber());
            $sheet->setCellValue('C1', 'Designation');
            $sheet->setCellValue('C'.$i, $result->getDesignation());
            $sheet->setCellValue('D1', 'Description');
            $sheet->setCellValue('D'.$i, $result->getDescribing());
            $sheet->setCellValue('E1', 'Coût de revient');
            $sheet->setCellValue('E'.$i, $result->getCostPrice());
            $sheet->setCellValue('F1', 'Prix de référence');
            $sheet->setCellValue('F'.$i, $result->getReferencePrice());
            $sheet->setCellValue('G1', 'Prix spécial');
            $sheet->setCellValue('G'.$i, $result->getSpecialPrice());
            $sheet->setCellValue('H1', 'Date_de_production');
            $sheet->setCellValue('H'.$i, $result->getProductionDate());
            $sheet->setCellValue('I1', 'Date de sortie');
            $sheet->setCellValue('I'.$i, $result->getLeftDate());
            $sheet->setCellValue('J1', 'Visible');
            $sheet->setCellValue('J'.$i, $result->getIsvisible());
            $sheet->getColumnDimension($b)->setAutoSize(true);
        }
        
        $sheet->setTitle("Produits".date('d-m-Y'));
        
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'Listing produits'.date('d-m-Y').'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE); 
    }

    /**
     * @Route("/user/exportPDF", name="user_export_articles_in_PDF")
     */
    public function sendDatasByEmail(DashboardUserService $service, MailerInterface $mailer){
        extract($_POST);

        $user = $this->getUser()->getId();
        $client = $this->getUser();
        $date = new DateTime('now');

        $results = $service->getAllArticlesASC($user);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('User/workshop_stock/workshop_stock_PDF_data.html.twig', [
            'articles' => $results,
            'date' => $date
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();
        
        // Write file to the desired path
        file_put_contents('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf', $output);

        // Create the message
            $message = (new TemplatedEmail())
                // Sender
                ->from('barri@barri.fr')
                // Recipient
                ->to($email)
                // Subject
                ->subject('Tableau de produit')
                // Content
                ->htmlTemplate('User/workshop_stock/partials/_mail_all_articles.html.twig')
                ->attachFromPath('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf');
            
            // Send the mail
            $mailer->send($message);

            unlink('../assets/documents/mypdf-'.$client->getLastName().'-'.date('d-m-Y').'.pdf');

        $this->addFlash("success","L'email à été envoyé avec succès");

        return $this->redirectToRoute('user_workshop_stock');
    }

    // /**
    //  * @Route("/user/printArticles", name="user_print_datas")
    //  */
    // public function printDatas(DashboardUserService $service){

    //     $user = $this->getUser()->getId();
    //     $date = new DateTime('now');

    //     $results = $service->getAllArticlesASC($user);

    //     // Configure Dompdf according to your needs
    //     $pdfOptions = new Options();
    //     $pdfOptions->set('defaultFont', 'Arial')
    //             ->setIsHtml5ParserEnabled(true)
    //             ->setIsRemoteEnabled(true);
        
    //     // Instantiate Dompdf with our options
    //     $dompdf = new Dompdf($pdfOptions);
        
    //     // Retrieve the HTML generated in our twig file
    //     $html = $this->renderView('User/workshop_stock/workshop_stock_PDF_data.html.twig', [
    //         'articles' => $results,
    //         'date' => $date
    //     ]);
        
    //     // Load HTML to Dompdf
    //     $dompdf->loadHtml($html);
        
    //     // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    //     $dompdf->setPaper('A4', 'landscape');

    //     // Render the HTML as PDF
    //     $dompdf->render();

    //     // Store PDF Binary Data
    //     $output = $dompdf->output();
        
    //     return new Response($output, 200, [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'inline; filename="myfilename.pdf"'
    //     ]);
    // }

}