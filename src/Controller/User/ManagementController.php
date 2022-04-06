<?php

namespace App\Controller\User;

use DateTime;
use App\Form\UserConfigurationType;
use App\Repository\VentesRepository;
use App\Service\DashboardUserService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserConfigurationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ManagementController extends AbstractController{

    /**
     * @Route("/user/management", name="user_management")
     */
    public function indexManagement(DashboardUserService $service, EntityManagerInterface $manager, UserConfigurationRepository $repo, Request $request, VentesRepository $repo_vente){

        $user = $this->getUser()->getId();
        $client = $this->getUser();

        //vente
        $vente = $repo_vente->findBy(["client" => $user], ["id" => "DESC"]);

        // Edit the user last action
        $client->setLast_action(new \DateTime("now"));
        $manager->persist($client);

        // Request for the management
        $allSoldArticles = $service->getAllSoldArticles($user);

        
        // Get the user configuartion
        $user_configuration = $repo->findOneBy(['client' => $client]);

        $superTopConfig = $user_configuration->getMaxsupertop();
        $topConfig = $user_configuration->getMaxtop();

        // Create array & addition of items inside according to its configuration
        $top = [];
        $superTop = [];
        $notTop = [];
        // $totalRevenue = 0;
        // $countArticle = 0;

        $allSoldArticle = $service->getCountSoldArticle($client);

        foreach ($allSoldArticles as $article) {

            $date_diff = date_diff($article->getDateVente(),new DateTime('now'));
            // dump($date_diff->format('%m'));
            // dump($date_diff->format('%Y'));

            // TURNOVER IN THE LAST 12 MONTHS AND CALCULATES THE QUANTITY OF ARTICLE SOLD OVER THE LAST 12 MONTHS
            // if ($article->getSpecialPrice() && $date_diff->format('%m') <= 12 && $date_diff->format('%Y') == 00) {
            //     $totalRevenue += $article->getSpecialPrice();
            //     $countArticle += 1;
            // } else if($date_diff->format('%m') <= 12 && $date_diff->format('%Y') == 00){
            //     $totalRevenue += $article->getReferencePrice();
            //     $countArticle += 1;
            // } else{}

        //    if ($article->getLeftdate() == null) {
        //        $article->setLeftdate(new DateTime('now')) ;
        //    }
            // CALCULATION OF THE INTERVAL BETWEEN THE PRODUCTION DATE AND THE RELEASE DATE AND THEN PUT IN A COLUMN ACCORDING TO THE RESULT
            $interval = date_diff($article->getDateVente(),$article->getArticle()->getProductiondate());

            if ($interval->format('%m') <= $superTopConfig && $interval->format('%Y') == 00) {
                $superTop [] = [$article->getArticle()->getDesignation()];
            } elseif($interval->format('%m') > $superTopConfig && $interval->format('%m') <= $topConfig  && $interval->format('%Y') == 00){
                $top [] = [$article->getArticle()->getDesignation()];
            } else {
                $notTop [] = [$article->getArticle()->getDesignation()];
            }
        }

        $form = $this->createForm(UserConfigurationType::class,$user_configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user_configuration);
            $manager->flush();
            // dd();
            $this->addFlash("success","Votre configuration à bien été enregistré");
            return $this->redirectToRoute('user_management');
        }
        

        // Return information to the twig template
        return $this->render('User/management/index_management.html.twig', [
            // 'totalRevenue' => $totalRevenue,
            // 'countArticle' => $countArticle,
            'superTop' => $superTop,
            'top' => $top,
            'notTop' => $notTop,
            'topConfig' => $topConfig,
            'superTopConfig' => $superTopConfig,
            'form' => $form->createView(),
            'vente' => $vente,
            'allSoldArticle' =>$allSoldArticle
        ]);
    }

    /**
     * EXPORT ALL SOLD ARTICLE CSV
     * 
     * @Route("/user/exportSoldArticleCSV", name="soldArticleCSV")
     */
    public function ExportArticlesInCSV(DashboardUserService $service){
        $user = $this->getUser()->getId();

        $results = $service->getSoldArticleCSV($user);

        $spreadsheet = new Spreadsheet();
        
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $i = 1;
        $b = 'A';
        foreach ($results as $result){
            $i++;
            $b++;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'Numéro');
            $sheet->setCellValue('B'.$i, $result->getArticle()->getNumber());
            $sheet->setCellValue('C1', 'Designation');
            $sheet->setCellValue('C'.$i, $result->getArticle()->getDesignation());
            $sheet->setCellValue('D1', 'Date de vente');
            $sheet->setCellValue('D'.$i, $result->getDateVente());
            $sheet->setCellValue('E1', 'Quantité vendu');
            $sheet->setCellValue('E'.$i, $result->getQuantity());
            $sheet->setCellValue('F1', 'Total');
            $sheet->setCellValue('F'.$i, $result->getPrice());
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

}