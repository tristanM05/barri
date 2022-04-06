<?php

namespace App\Controller\User;

use App\Data\SearchData;
use App\Form\SearchType;
use App\Entity\Decaissement;
use App\Entity\Encaissement;
use App\Form\DecaissementType;
use App\Form\EncaissementType;
use App\Repository\ArticleRepository;
use App\Repository\CaisseRepository;
use App\Repository\JournalRepository;
use App\Service\DashboardUserService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\DecaissementRepository;
use App\Repository\EncaissementRepository;
use App\Repository\VentesRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JournalController extends AbstractController
{
    /**
     * @Route("/journal", name="journal")
     */
    public function index(ArticleRepository $repo, Decaissement $dec = null, Request $req, EntityManagerInterface $manager, 
    DecaissementRepository $repo_dec, EncaissementRepository $repo_encaissement, Encaissement $enc = null, JournalRepository $journal_repo, 
    DashboardUserService $service, CaisseRepository $repo_caisse, VentesRepository $repo_vente)
    {
        $client = $this->getUser();
        
        $date = new \DateTime('now');
        $date->setTime(0,0,0);
        $journal2 = $journal_repo->findOneBy(['date' => $date, 'client' => $client]);
        $caisse = $repo_caisse->findBy(['journalId' => $journal2], ["date" => "DESC"]);


  //gestion form decaissement
        $totalDec = $service->getdecOfDay($client);
        $now = new \DateTime('now');
        $client = $this->getUser();
        if(!$dec){
            $dec = new Decaissement();
        }
        $formD = $this->createForm(DecaissementType::class, $dec);
        $formD->handleRequest($req);

        $totalDecaissementOfJournal = $journal2->getTotalDec();
        $totalEncaissementOfJournal = $journal2->getTotalEnc();
        $totalStartOfJournal = $journal2->getTotalStart();

        if($formD->isSubmitted() && $formD->isValid()){

            $dec->setDate($now);
            $dec->setUser($client);
            $dec->setJournal($journal2);
            $dec->setIsEnc(0);

            
            /*** ASIGN PAYMENT MODE***/
            $cbDec = $dec->getCb();
            $espDec = $dec->getEsp();
            $chqDec = $dec->getChq();
            $otherDec = $dec->getOther();
            $totalJournalCbDec = $journal2->getTotalCb();
            $totalJournalEspDec = $journal2->getTotalEsp();
            $totalJournalChqDec = $journal2->getTotalChq();
            $totalJournalOtherDec = $journal2->getTotalOther();

            if($cbDec != null){
                $dec->setTotal($cbDec);
                $journal2->setTotalCb($totalJournalCbDec-$cbDec);
            }elseif($espDec != null){
                $dec->setTotal($espDec);
                $journal2->setTotalEsp($totalJournalEspDec-$espDec);
            }elseif($chqDec != null){
                $dec->setTotal($chqDec);
                $journal2->setTotalChq($totalJournalChqDec-$chqDec);
            }elseif($otherDec != null){
                $dec->setTotal($otherDec);
                $journal2->setTotalOther($totalJournalOtherDec-$otherDec);
            }
            /*** ASIGN PAYMENT MODE***/

            $totalDecaissementForJournal = $dec->getTotal();
            $journal2->setTotalDec($totalDecaissementOfJournal+$totalDecaissementForJournal);
            $journal2->setTotalEnd($totalStartOfJournal+$totalEncaissementOfJournal-$totalDecaissementForJournal-$totalDecaissementOfJournal);

            $manager->persist($dec);
            $manager->persist($journal2);
            $manager->flush();
            $this->addFlash("success", "la modification a bien été éffectuée");
            return $this->redirectToRoute("journal");
        }
        
        $decaissement = $repo_dec->findBy(['user' => $client], ["id" => "DESC"]);

        //gestion encaissement
        // $encaissements = $repo_encaissement->findBy(['client' => $client], ["id" => "DESC"]);
        $totalEnc = $service->getencOfDay($client);
        if(!$enc){
            $enc = new Encaissement();
        }
        $formE = $this->createForm(EncaissementType::class, $enc);
        $formE->handleRequest($req);
        if($formE->isSubmitted() && $formE->isValid()){

            $enc->setDate($now);
            $enc->setClient($client);
            $enc->setJournal($journal2);
            $enc->setIsEnc(1);

            /*** ASIGN PAYMENT MODE***/
            $cb = $enc->getCb();
            $esp = $enc->getEsp();
            $chq = $enc->getChq();
            $other = $enc->getOther();
            $totalJournalCb = $journal2->getTotalCb();
            $totalJournalEsp = $journal2->getTotalEsp();
            $totalJournalChq = $journal2->getTotalChq();
            $totalJournalOther = $journal2->getTotalOther();

            if($cb != null){
                $enc->setTotal($cb);
                $journal2->setTotalCb($cb+$totalJournalCb);
            }elseif($esp != null){
                $enc->setTotal($esp);
                $journal2->setTotalEsp($esp+$totalJournalEsp);
            }elseif($chq != null){
                $enc->setTotal($chq);
                $journal2->setTotalChq($chq+$totalJournalChq);
            }elseif($other != null){
                $enc->setTotal($other);
                $journal2->setTotalOther($other+$totalJournalOther);
            }
            /*** ASIGN PAYMENT MODE***/

            $totalEncaissementForJournal = $enc->getTotal();
            $journal2->setTotalEnc($totalEncaissementOfJournal+$totalEncaissementForJournal);
            $journal2->setTotalEnd($totalStartOfJournal+$totalEncaissementForJournal+$totalEncaissementOfJournal-$totalDecaissementOfJournal);

            $manager->persist($journal2);
            $manager->persist($enc);
            $manager->flush();

            $this->addFlash("success", "la modification a bien été éffectuée");
            return $this->redirectToRoute("journal");
        }
        $encaissement = $repo_encaissement->findBy(["journal" => $journal2]);
        // foreach($encaissement as $e){
        //     $encId = $e->getId();
        //     $encFromCaisse = $repo_caisse->findEncFromCaisse($encId);
        // }
        // $encaissement = $repo_encaissement->findOneBy(["id" => 193]);
        // $caisse21 = $repo_caisse->findBy(['id' => $encaissement]);
        // dd($caisse21);   
        // $e = []; 
        // foreach($caisse as $c){
        //     $cId = $c->getid();
        //     $e = $repo_encaissement->findOneBy(["id" => $cId]);
        // }
        // dd($encFromCaisse);

        $data = new SearchData();
        $formSearch = $this->createForm(SearchType::class, $data);
        $formSearch->handleRequest($req);
        $journal = $journal_repo->findSearchEnc($data, $client);
        $caisse2 = $repo_caisse->findBy(['journalId' => $journal], ["date" => "DESC"]);
        $submited = $formSearch->isSubmitted();

        /*** MODAL VENTE ***/
        $vente = $repo_vente->findBy(["client" => $client], ["id" => "DESC"]);
        /*** MODAL VENTE ***/

        return $this->render('User/journal/index2.html.twig', [
            'form' => $formD->createView(),
            'formEnc' => $formE->createView(),
            'formSearch' => $formSearch->createView(),
            'dec' => $decaissement,
            'journal' => $journal,
            'date' => $date,
            'totalDec' => $totalDec,
            'totalEnc' => $totalEnc,
            'journalCurrentDay' => $journal2,
            'submited' => $submited,
            'caisse' => $caisse,
            'caisse2' => $caisse2,
            'vente' => $vente,
            // 'encFromCaisse' => $encFromCaisse
        ]);
    }

    /**
     * Undocumented function
     * @Route("/journal/csvToday", name="csvToday")
     * @param JournalRepository $repo_journal
     * @return void
     */
    public function exportCsv(JournalRepository $repo_journal, EncaissementRepository $repo_encaissement, DecaissementRepository $repo_decaissement, VentesRepository $repo_vente){

        $date = new \DateTime('now');
        $date->setTime(0,0,0);
        $user = $this->getUser()->getId();
        $results = $repo_journal->findBy(['date' => $date, 'client' => $user]);
        $encaissements = $repo_encaissement->findBy(['journal' => $results], ["id" => "DESC"]);
        $decaissement = $repo_decaissement->findBy(['journal' => $results], ["id" => "DESC"]);
        $ventes = $repo_vente->findBy(['encaissement' => $encaissements], ["id" => "ASC"]);

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
            $sheet->setCellValue('B1', 'Date');
            $sheet->setCellValue('C1', 'Encaissements');
            $sheet->setCellValue('D1', 'Décaissements');
            $sheet->setCellValue('E1', 'Commentaire');
            $sheet->setCellValue('F1', 'CB');
            $sheet->setCellValue('G1', 'Espèces');
            $sheet->setCellValue('H1', 'Chèques');
            $sheet->setCellValue('I1', 'Autre');
            $sheet->setCellValue('k1', 'Total début de journée');
            $sheet->setCellValue('L'.$i, $result->getTotalStart());
            $sheet->setCellValue('L1', 'Total fin de journée');
            $sheet->setCellValue('L'.$i, $result->getTotalEnd());
            $sheet->getColumnDimension($b)->setAutoSize(true);

            foreach($encaissements as $enc){
                $i++;
                $b++;
                $sheet->setCellValue('B'.$i, $enc->getDate());
                $sheet->setCellValue('C'.$i, $enc->getTotal());
                $sheet->setCellValue('E'.$i, $enc->getComment());
                $sheet->setCellValue('F'.$i, $enc->getCb());
                $sheet->setCellValue('G'.$i, $enc->getEsp());
                $sheet->setCellValue('H'.$i, $enc->getChq());
                $sheet->setCellValue('I'.$i, $enc->getOther());
            }
            foreach($decaissement as $dec){
                $i++;
                $b++;
                $sheet->setCellValue('B'.$i, $dec->getDate());
                $sheet->setCellValue('D'.$i, $dec->getTotal());
                $sheet->setCellValue('E'.$i, $dec->getComment());
                $sheet->setCellValue('F'.$i, $dec->getCb());
                $sheet->setCellValue('G'.$i, $dec->getEsp());
                $sheet->setCellValue('H'.$i, $dec->getChq());
                $sheet->setCellValue('I'.$i, $dec->getOther());
            }
        }
        $sheet2 = $spreadsheet->createSheet();
        
        foreach ($ventes as $vente){
            $i2++;
            $b2++;
            $sheet2->setCellValue('B1', 'Date');
            $sheet2->setCellValue('B'.$i2, $vente->getDateVente());
            $sheet2->setCellValue('C1', 'Numéro');
            $sheet2->setCellValue('C'.$i2, $vente->getArticle()->getNumber());
            $sheet2->setCellValue('D1', 'Nom');
            $sheet2->setCellValue('D'.$i2, $vente->getArticle()->getDesignation());
            $sheet2->setCellValue('E1', 'Quantité');
            $sheet2->setCellValue('E'.$i2, $vente->getQuantity());
            $sheet2->setCellValue('F1', 'Total');
            $sheet2->setCellValue('F'.$i2, $vente->getPrice());
            $sheet2->getColumnDimension($b2)->setAutoSize(true);
        }
        
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'Journal de caisse du'.date('d-m-Y').'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE); 
    }

    
    /**
     * Undocumented function
     * @Route("/journal/csvDate", name="csvDate")
     * @param JournalRepository $repo_journal
     * @return void
     */
    public function exportCsv2(JournalRepository $repo_journal, EncaissementRepository $repo_encaissement, DecaissementRepository $repo_decaissement, VentesRepository $repo_vente){

        
        $user = $this->getUser()->getId();

        // $dateJournal = $_POST['date'] ;

        
        $results = $repo_journal->findBy(['client' => $user]);
        $encaissements = $repo_encaissement->findBy(['journal' => $results], ["id" => "DESC"]);
        $decaissement = $repo_decaissement->findBy(['journal' => $results], ["id" => "DESC"]);
        $ventes = $repo_vente->findBy(['encaissement' => $encaissements], ["id" => "ASC"]);

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
            $sheet->setCellValue('B1', 'Date');
            $sheet->setCellValue('C1', 'Encaissements');
            $sheet->setCellValue('D1', 'Décaissements');
            $sheet->setCellValue('E1', 'Commentaire');
            $sheet->setCellValue('F1', 'CB');
            $sheet->setCellValue('G1', 'Espèces');
            $sheet->setCellValue('H1', 'Chèques');
            $sheet->setCellValue('I1', 'Autre');
            $sheet->setCellValue('k1', 'Total début de journée');
            $sheet->setCellValue('L'.$i, $result->getTotalStart());
            $sheet->setCellValue('L1', 'Total fin de journée');
            $sheet->setCellValue('L'.$i, $result->getTotalEnd());
            $sheet->getColumnDimension($b)->setAutoSize(true);

            foreach($encaissements as $enc){
                $i++;
                $b++;
                $sheet->setCellValue('B'.$i, $enc->getDate());
                $sheet->setCellValue('C'.$i, $enc->getTotal());
                $sheet->setCellValue('E'.$i, $enc->getComment());
                $sheet->setCellValue('F'.$i, $enc->getCb());
                $sheet->setCellValue('G'.$i, $enc->getEsp());
                $sheet->setCellValue('H'.$i, $enc->getChq());
                $sheet->setCellValue('I'.$i, $enc->getOther());
            }
            foreach($decaissement as $dec){
                $i++;
                $b++;
                $sheet->setCellValue('B'.$i, $dec->getDate());
                $sheet->setCellValue('D'.$i, $dec->getTotal());
                $sheet->setCellValue('E'.$i, $dec->getComment());
                $sheet->setCellValue('F'.$i, $dec->getCb());
                $sheet->setCellValue('G'.$i, $dec->getEsp());
                $sheet->setCellValue('H'.$i, $dec->getChq());
                $sheet->setCellValue('I'.$i, $dec->getOther());
            }
        }
        $sheet2 = $spreadsheet->createSheet();
        
        foreach ($ventes as $vente){
            $i2++;
            $b2++;
            $sheet2->setCellValue('B1', 'Date');
            $sheet2->setCellValue('B'.$i2, $vente->getDateVente());
            $sheet2->setCellValue('C1', 'Numéro');
            $sheet2->setCellValue('C'.$i2, $vente->getArticle()->getNumber());
            $sheet2->setCellValue('D1', 'Nom');
            $sheet2->setCellValue('D'.$i2, $vente->getArticle()->getDesignation());
            $sheet2->setCellValue('E1', 'Quantité');
            $sheet2->setCellValue('E'.$i2, $vente->getQuantity());
            $sheet2->setCellValue('F1', 'Total');
            $sheet2->setCellValue('F'.$i2, $vente->getPrice());
            $sheet2->getColumnDimension($b2)->setAutoSize(true);
        }
        
        // $sheet->setTitle("journal de caisse du".date('d-m-Y'));
        
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'Journal de caisse du'.date('d-m-Y').'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE); 
    }
}
