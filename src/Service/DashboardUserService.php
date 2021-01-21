<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class DashboardUserService{
    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }

    // public function getStockValue($client){
    //     return $this->manager->createQuery('SELECT sum(a.referenceprice) as SUM from App\Entity\Article a join a.productStatus p where a.client = ?1 and a.isvisible = 1 and p.wording = ?2')
    //     ->setParameter(1,$client)
    //     ->setParameter(2,'en vente')
    //     ->getResult();
    // }

    // public function getStockValueBySalepoint($client){
    //     return $this->manager->createQuery('SELECT sum(a.referenceprice) as SUM, s.wording from App\Entity\Article a join a.productStatus p join a.salepoint s where a.client = ?1 and a.isvisible = 1 and p.wording = ?2 GROUP BY a.salepoint')
    //     ->setParameter(1,$client)
    //     ->setParameter(2,'en vente')
    //     ->getResult();
    // }

    // public function getProducedProducts($client,$start_date,$end_date){
    //     $tab_start_date = explode('/',$start_date);
    //     $new_start_date = $tab_start_date[2].'-'.$tab_start_date[1].'-'.$tab_start_date[0].' 00:00:00';
        
    //     $tab_end_date = explode('/', $end_date);
    //     $new_end_date = $tab_end_date[2].'-'.$tab_end_date[1].'-'.$tab_end_date[0].' 23:59:59';

    //     $product_query = $this->manager->createQuery('SELECT count(a) FROM App\Entity\Article a where a.client = ?3 and a.isvisible = 1 and a.productiondate BETWEEN ?1 and ?2 ')
    //     ->setParameter(1, $new_start_date)
    //     ->setParameter(2, $new_end_date)
    //     ->setParameter(3, $client)
    //     ->getResult();
        

    //     return compact('product_query', 'new_end_date', 'new_start_date');
    // }

    // public function getSoldProducts($client, $start_date, $end_date){
    //     $tab_start_date = explode('/',$start_date);
    //     $new_start_date = $tab_start_date[2].'-'.$tab_start_date[1].'-'.$tab_start_date[0].' 00:00:00';
        
    //     $tab_end_date = explode('/', $end_date);
    //     $new_end_date = $tab_end_date[2].'-'.$tab_end_date[1].'-'.$tab_end_date[0].' 23:59:59';

    //     $product_query = $this->manager->createQuery('SELECT count(a), sum(a.referenceprice) as SUM FROM App\Entity\Article a join a.productStatus p where a.client = ?3 and a.isvisible = 1 and p.wording = ?4 and a.leftdate BETWEEN ?1 and ?2 ')
    //     ->setParameter(1, $new_start_date)
    //     ->setParameter(2, $new_end_date)
    //     ->setParameter(3, $client)
    //     ->setParameter(4, 'vendu le')
    //     ->getResult();

    //     return compact('product_query', 'new_end_date', 'new_start_date');
    // }

    public function getStockAndValue($client){

        // $tab_date = explode('/',$date);
        // $new_date = $tab_date[2].'-'.$tab_date[1].'-'.$tab_date[0].' 23:59:59';

        $stock_query = $this->manager->createQuery('SELECT sum(a.referenceprice) as SUM, count(a.referenceprice) as COUNT from App\Entity\Article a join a.productStatus p where a.client = ?1 and a.isvisible = 1 and p.wording = ?2')
        ->setParameter(1,$client)
        ->setParameter(2,'en vente')
        // ->setParameter(3, $new_date)
        ->getResult();

        return compact('stock_query');
    }

    public function getAllSoldArticles($client){
        return $this->manager->createQuery('SELECT a from App\Entity\Article a where a.client = ?1 and a.isvisible = 1 and a.productStatus != 1 order BY a.designation ASC')
        ->setParameter(1, $client)
        ->getResult();
    }

    public function getAllArticlesASC($client){
        return $this->manager->createQuery('SELECT a from App\Entity\Article a where a.client = ?1  order BY a.designation ASC')
        ->setParameter(1, $client)
        ->getResult();
    }

    public function getStorageLifeAvg($client){
        return $this->manager->createQuery('SELECT AVG(date_diff(a.leftdate, a.productiondate)) as AVG from App\Entity\Article a where a.client = ?1 and a.isvisible = 1')
        ->setParameter(1, $client)
        ->getResult();
    }

    public function getAlert1($user, $date){
        return $this->manager->createQuery('SELECT a from App\Entity\Article a where a.client = ?1 and a.dateLimit = ?2 and a.productStatus = 1')
        ->setParameter(1, $user)
        ->setParameter(2,$date)
        ->getResult();
    }

    public function getExpired($client, $now){
        return $this->manager->createQuery('SELECT a from App\Entity\Article a where a.client = ?1 and a.endDate < ?2 and a.productStatus = 1 order by a.endDate DESC')
        ->setParameter(1, $client)
        ->setParameter(2,$now)
        ->getResult();
    }




}