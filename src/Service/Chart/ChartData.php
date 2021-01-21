<?php

namespace App\Service\Chart;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class ChartData{

    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }

    public function getValueAndQuantityStock($client){
        // return $this->manager->createQuery('SELECT sum(a.referenceprice) as SUM, a.productiondate from App\Entity\Article a join a.productStatus p where a.client = ?2 and p.wording = ?1 group by a.productiondate')
        //     ->setParameter(1, 'en vente')
        //     ->setParameter(2, $client)
        //     ->getResult();

        return $this->manager->createQuery('SELECT s.quantity, s.value, s.date from App\Entity\StockClient s where s.client = ?1 order by s.date DESC')
                ->setParameter(1,$client)
                ->setMaxResults(10)
                ->getResult();

        // return $this->manager->createQuery('SELECT sum(a.referenceprice) as SUM, a.productiondate from App\Entity\Article a join a.productStatus p where a.client = 90group by a.productiondate')
        //     ->getResult();
    }
}