<?php

namespace App\Command;

use DateTime;
use App\Entity\Article;
use App\Entity\StockClient;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Alert1 extends Command{
    
    protected static $defaultName = 'app:alert-1';
    private $manager;
    private $repo;

    public function __construct(EntityManagerInterface $manager, ClientRepository $repo){
        $this->manager = $manager;
        $this->repo = $repo;
        parent::__construct();
    }

    protected function configure(){
        $this
            ->setDescription('Get the value and the count of the stock by day & user')
            ->setHelp('Allow to get the value and the count of the stock by day & user')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){

        $now = new \DateTime('now');
        $now->setTime(0,0,0);
    
        $alert = $this->manager->createQuery('SELECT a from App\Entity\Article a where a.dateLimit = ?1 and a.productStatus = 1')
            ->setParameter(1,$now)
            ->getResult();

        foreach($alert as $datassq){
            
        };

        $output->write('It works');
        
        return 0;
    }
}