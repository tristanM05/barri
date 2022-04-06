<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class DashboardAdminService{
    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }
    

    public function getCountUsers(){
        return $this->manager->createQuery("SELECT count(c) as countUsers from App\Entity\Client c where c.roles != ?1")
            ->setParameter(1,"ROLE_ADMIN")
            ->getResult();
    }

    public function getAllUsers(){
        return $this->manager->createQuery("SELECT c from App\Entity\Client c where c.roles != 'ROLE_ADMIN'")
            ->getResult();
    }

    public function getCountItems(){
        return $this->manager->createQuery("SELECT sum(a.quantity) as countItems from App\Entity\Article a")
            ->getResult();
    }

    public function getCountStock(){
        return $this->manager->createQuery('SELECT sum(a.totalPrice) as SUM from App\Entity\Article a join a.productStatus p where p.wording = ?1')
            ->setParameter(1,'en vente')
            ->getResult();
    }

    public function getCountConnectedUSers($date){
        return $this->manager->createQuery("SELECT count(c) from App\Entity\Client c where date_diff(?1, c.last_action) = 0 and c.roles != ?2")
            ->setParameter(1,$date)
            ->setParameter(2,'ROLE_ADMIN')
            ->getResult();
    }

    public function getCountItemByUser($id){
        return $this->manager->createQuery("SELECT sum(a.quantity) as countItems from App\Entity\Article a join a.client c where c.id = ?1")
            ->setParameter(1, $id)
            ->getResult();
    }

    public function getValueStock($id){
        return $this->manager->createQuery("SELECT sum(a.totalPrice) as val from App\Entity\Article a join a.client c join a.productStatus p where c.id = ?1 and p.wording = ?2")
            ->setParameter(1, $id)
            ->setParameter(2, 'en vente')
            ->getResult();
    }


}