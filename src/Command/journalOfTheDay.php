<?php

namespace App\Command;

use DateTime;
use App\Entity\Client;
use App\Entity\Journal;
use App\Repository\ClientRepository;
use App\Repository\JournalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class journalOfTheDay extends Command{
    protected static $defaultName = 'app:journal';
    private $manager;
    private $repo;
    private $repo_journal;
    
    public function __construct(EntityManagerInterface $manager, ClientRepository $repo, JournalRepository $repo_journal){
        $this->manager = $manager;
        $this->repo = $repo;
        $this->repo_journal = $repo_journal;
        parent::__construct();
    }
    
    protected function configure(){
        $this
        ->setDescription('create journal of the day')
        ->setHelp('create journal of the day')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){

        $users = $this->manager->createQuery('SELECT c.id from App\Entity\Client c')
            ->getResult();
        

        foreach ($users as $datssq) {
            $client = $this->repo->findOneBy(['id' => $datssq['id']] );

            $date = new \DateTime('now');
            $date->setTime(0,0,0);

            $yesterday = new \DateTime('now');
            $yesterday->setTime(0,0,0);
            $yesterday->modify('-1 day');

            $journalYesterdays = $this->manager->createQuery('SELECT j from App\Entity\Journal j where j.date = ?1 and j.client = ?2')
            ->setParameter(1, $yesterday)
            ->setParameter(2, $client)
            ->getResult();

            if($journalYesterdays != null){
                foreach($journalYesterdays as $journalYesterday){
                    $idJournal = $journalYesterday->getId();
                    $journalY = $this->repo_journal->findOneBy(['id' => $idJournal]);
                    // $totalEnd = $journalY->getTotalEnd();
                    $journalYEsp = $journalY->getTotalEsp();
                    $journalYEspStart = $journalY->getTotalEspStart();
                    $journalYChq = $journalY->getTotalChq();
                    $journalYOther = $journalY->getTotalOther();
                    $totalEnd = $journalYEsp + $journalYChq + $journalYOther;
                    $client = $this->repo->findOneBy(['id' => $datssq['id']] );
                    $journal = new Journal();
                    $journal->setClient($client)
                        ->setDate($date)
                        ->setTotalStart($totalEnd + $journalYEspStart)
                        ->setTotalEspStart($journalYEsp + $journalYEspStart)
                        ->setTotalCb(0.00)
                        ->setTotalEsp(0.00)
                        ->setTotalChq(0.00)
                        ->setTotalOther(0.00);
                    $this->manager->persist($journal);
                    $this->manager->flush();
                }
            }else{
                $client = $this->repo->findOneBy(['id' => $datssq['id']] );
                $journal = new Journal();
                $journal->setClient($client)
                    ->setDate($date)
                    ->setTotalStart(0.00)
                    ->setTotalEspStart(0.00)
                    ->setTotalCb(0.00)
                    ->setTotalEsp(0.00)
                    ->setTotalChq(0.00)
                    ->setTotalOther(0.00);
                $this->manager->persist($journal);
                $this->manager->flush();
            }
        }
        $output->write('It works');
        
        return 0;
    }
}