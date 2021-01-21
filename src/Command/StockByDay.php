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

class StockByDay extends Command{
    
    protected static $defaultName = 'app:stock-by-day';
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

        $users = $this->manager->createQuery('SELECT c.id, COUNT(a) as quantity, SUM(a.referenceprice) as value from App\Entity\Article a join a.client c where a.isvisible = ?1 and a.leftdate IS NULL GROUP BY c.id')
            ->setParameter(1, 1)
            ->getResult();

        foreach ($users as $datssq) {
            $client_for_stock = $this->repo->findOneBy(['id' => $datssq['id']] );
            $stock = new StockClient();
            $stock->setClient($client_for_stock)
                ->setQuantity($datssq['quantity'])
                ->setValue($datssq['value'])
                ->setDate(new DateTime('now'));
            $this->manager->persist($stock);
            $this->manager->flush();
        }


        $output->write('It works');
        
        return 0;
    }
}