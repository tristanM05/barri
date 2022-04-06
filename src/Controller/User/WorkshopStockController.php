<?php

namespace App\Controller\User;

use App\Entity\Article;
use App\Entity\Client;
use App\Entity\ProductStatus;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Form\FilterArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ClientRepository;
use App\Repository\LotsRepository;
use App\Repository\ProductStatusRepository;
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
        /** GRAPH STOCK AND VALUE **/
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
        /** GRAPH STOCK AND VALUE **/

        /** ALERT ARTICLE **/
        $nowAlert = new \DateTime('now');
        $nowAlert->setTime(0,0,0);
        $alert = $repo->findBy(['client' => $client, 'alert' => $nowAlert, 'productStatus' => 1]);
        /** ALERT ARTICLE **/

        /** EXPIRATION DATE **/
        $now = new \DateTime('now');
        $expired = $service_request->getExpired($client, $now);
        /** EXPIRATION DATE **/

        /** LIMIT ARTICLE (10) BASIC ACCOUNT **/
            $count = $service_request->getAllArticle($user);
        /** LIMIT ARTICLE (10) BASIC ACCOUNT **/

        /** ALERT STOCK **/
        $alertStock = $service_request->getAlertStock($client);
        /** ALERT STOCK **/

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
            'now' => $nowAlert,
            'alert' => $alert,
            'expired' =>$expired,
            'count' => $count,
            'alertStock' => $alertStock
        ]);
    }

    /**
     * @Route("/user/exportCSV", name="user_export_articles_in_CSV")
     */
    public function ExportArticlesInCSV(DashboardUserService $service, LotsRepository $repo_lot){
        $user = $this->getUser()->getId();

        $results = $service->getAllArticlesASC($user);
        $lots = $repo_lot->findBy(['article' => $results]);
        $spreadsheet = new Spreadsheet();
        
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $i = 1;
        $b = 'A';
        $i2 = 1;
        $b2 = 'A';
        foreach ($results as $result){
            $i++;
            $b++;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'Numéro');
            $sheet->setCellValue('B'.$i, $result->getNumber());
            $sheet->setCellValue('C1', 'Designation');
            $sheet->setCellValue('C'.$i, $result->getDesignation());
            $sheet->setCellValue('D1', 'Catégorie');
            if($result->getCategory() != null){
                $sheet->setCellValue('D'.$i, $result->getCategory()->getName());
            }else{

            }
            $sheet->setCellValue('E1', 'Origine');
            if($result->getFournisseur() != null){
                $sheet->setCellValue('E'.$i, $result->getFournisseur()->getName());
            }else{

            }
            $sheet->setCellValue('F1', 'Description');
            $sheet->setCellValue('F'.$i, $result->getDescribing());
            $sheet->setCellValue('G1', 'Coût de revient');
            $sheet->setCellValue('G'.$i, $result->getCostPrice());
            $sheet->setCellValue('H1', 'Prix de référence');
            $sheet->setCellValue('H'.$i, $result->getReferencePrice());
            $sheet->setCellValue('I1', 'Prix spécial');
            $sheet->setCellValue('I'.$i, $result->getSpecialPrice());
            $sheet->setCellValue('J1', 'Date_de_production');
            $sheet->setCellValue('J'.$i, $result->getProductionDate());
            $sheet->setCellValue('K1', 'Date de sortie');
            $sheet->setCellValue('K'.$i, $result->getLeftDate());
            $sheet->setCellValue('L1', 'Quantité');
            $sheet->setCellValue('L'.$i, $result->getQuantity());
            $sheet->setCellValue('M1', 'Visible');
            $sheet->setCellValue('M'.$i, $result->getIsvisible());
            $sheet->setCellValue('N1', 'Etat');
            $sheet->setCellValue('N'.$i, $result->getProductStatus()->getWording());
            $sheet->getColumnDimension($b)->setAutoSize(true);

        }
        $sheet2 = $spreadsheet->createSheet();
        foreach($lots as $lot){
            $i2++;
            $b2++;
            $sheet2->setCellValue('B1', 'Identifiant de lot');
            $sheet2->setCellValue('B'.$i2, $lot->getNumber());
            $sheet2->setCellValue('C1', 'Identifiant de l\'article lié');
            $sheet2->setCellValue('C'.$i2, $lot->getArticle()->getNumber());
            $sheet2->setCellValue('D1', 'Date d\'entré');
            $sheet2->setCellValue('D'.$i2, $lot->getDateEnter());
            $sheet2->setCellValue('E1', 'Date d\'expiration');
            $sheet2->setCellValue('E'.$i2, $lot->getDateExp());
            $sheet2->setCellValue('F1', 'Quantité');
            $sheet2->setCellValue('F'.$i2, $lot->getQuantity());
            $sheet2->setCellValue('G1', 'Lieux de stockage');
            $sheet2->setCellValue('G'.$i2, $lot->getPlace());
            $sheet2->getColumnDimension($b2)->setAutoSize(true);
        }
        
        $sheet->setTitle("Produits".date('d-m-Y'));
        $sheet2->setTitle("Lots");
        
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
    public function sendDatasByEmail(DashboardUserService $service, MailerInterface $mailer, LotsRepository $repo_lot){
        extract($_POST);

        $user = $this->getUser()->getId();
        $client = $this->getUser();
        $date = new DateTime('now');

        $results = $service->getAllArticlesASC($user);
        $lots = $repo_lot->findBy(['article' => $results]);

        // // Configure Dompdf according to your needs
        // $pdfOptions = new Options();
        // $pdfOptions->set('defaultFont', 'Arial');
        // // Instantiate Dompdf with our options
        // $dompdf = new Dompdf($pdfOptions);
        // // Retrieve the HTML generated in our twig file
        // $html = $this->renderView('User/workshop_stock/workshop_stock_PDF_data.html.twig', [
        //     'articles' => $results,
        //     'date' => $date
        // ]);
        // // Load HTML to Dompdf
        // $dompdf->loadHtml($html);
        // // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        // $dompdf->setPaper('A4', 'landscape');
        // // Render the HTML as PDF
        // $dompdf->render();
        // // Store PDF Binary Data
        // $output = $dompdf->output();
        $spreadsheet = new Spreadsheet();
        
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $i = 1;
        $b = 'A';
        $i2 = 1;
        $b2 = 'A';
        foreach ($results as $result){
            $i++;
            $b++;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'Numéro');
            $sheet->setCellValue('B'.$i, $result->getNumber());
            $sheet->setCellValue('C1', 'Designation');
            $sheet->setCellValue('C'.$i, $result->getDesignation());
            $sheet->setCellValue('D1', 'Catégorie');
            if($result->getCategory() != null){
                $sheet->setCellValue('D'.$i, $result->getCategory()->getName());
            }else{

            }
            $sheet->setCellValue('E1', 'Origine');
            if($result->getFournisseur() != null){
                $sheet->setCellValue('E'.$i, $result->getFournisseur()->getName());
            }else{

            }
            $sheet->setCellValue('F1', 'Description');
            $sheet->setCellValue('F'.$i, $result->getDescribing());
            $sheet->setCellValue('G1', 'Coût de revient');
            $sheet->setCellValue('G'.$i, $result->getCostPrice());
            $sheet->setCellValue('H1', 'Prix de référence');
            $sheet->setCellValue('H'.$i, $result->getReferencePrice());
            $sheet->setCellValue('I1', 'Prix spécial');
            $sheet->setCellValue('I'.$i, $result->getSpecialPrice());
            $sheet->setCellValue('J1', 'Date_de_production');
            $sheet->setCellValue('J'.$i, $result->getProductionDate());
            $sheet->setCellValue('K1', 'Date de sortie');
            $sheet->setCellValue('K'.$i, $result->getLeftDate());
            $sheet->setCellValue('L1', 'Quantité');
            $sheet->setCellValue('L'.$i, $result->getQuantity());
            $sheet->setCellValue('M1', 'Visible');
            $sheet->setCellValue('M'.$i, $result->getIsvisible());
            $sheet->setCellValue('N1', 'Etat');
            $sheet->setCellValue('N'.$i, $result->getProductStatus()->getWording());
            $sheet->getColumnDimension($b)->setAutoSize(true);
        }
        $sheet2 = $spreadsheet->createSheet();
        foreach($lots as $lot){
            $i2++;
            $b2++;
            $sheet2->setCellValue('B1', 'Identifiant de lot');
            $sheet2->setCellValue('B'.$i2, $lot->getNumber());
            $sheet2->setCellValue('C1', 'Identifiant de l\'article lié');
            $sheet2->setCellValue('C'.$i2, $lot->getArticle()->getNumber());
            $sheet2->setCellValue('D1', 'Date d\'entré');
            $sheet2->setCellValue('D'.$i2, $lot->getDateEnter());
            $sheet2->setCellValue('E1', 'Date d\'expiration');
            $sheet2->setCellValue('E'.$i2, $lot->getDateExp());
            $sheet2->setCellValue('F1', 'Quantité');
            $sheet2->setCellValue('F'.$i2, $lot->getQuantity());
            $sheet2->setCellValue('G1', 'Lieux de stockage');
            $sheet2->setCellValue('G'.$i2, $lot->getPlace());
            $sheet2->getColumnDimension($b2)->setAutoSize(true);
        }
        
        $sheet->setTitle("Produits".date('d-m-Y'));
        $sheet2->setTitle("Lots");
        
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'Listing produits'.date('d-m-Y').'.xlsx';
        $writer->save('../assets/documents/csv-'.$fileName);

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
                ->attachFromPath('../assets/documents/csv-'.$fileName);
            
            // Send the mail
            $mailer->send($message);

            unlink('../assets/documents/csv-'.$fileName);

        $this->addFlash("success","L'email à été envoyé avec succès");

        return $this->redirectToRoute('user_workshop_stock');
    }

    /**
     * @Route("/user/printArticles", name="user_print_datas")
     */
    public function printDatas(DashboardUserService $service, LotsRepository $repo_lot){

        $user = $this->getUser()->getId();
        $date = new DateTime('now');

        $results = $service->getAllArticlesASC($user);
        $lots = $repo_lot->findBy(['article' => $results]);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial')
                ->setIsHtml5ParserEnabled(true)
                ->setIsRemoteEnabled(true);
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('User/workshop_stock/workshop_stock_PDF_data.html.twig', [
            'articles' => $results,
            'date' => $date,
            'lots' => $lots
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();
        
        
        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="myfilename.pdf"'
        ]);
    }

    /**
     * @Route("/user/card", name="card")
     */
    public function card(ArticleRepository $repo_article){


    }

}