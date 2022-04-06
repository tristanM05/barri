<?php

namespace App\Repository;

use App\Entity\Caisse;
use App\Entity\Ventes;
use App\Entity\Encaissement;
use App\Entity\Journal;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Ventes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ventes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ventes[]    findAll()
 * @method Ventes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VentesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ventes::class);
    }

    public function findEncFromCaisse()
    {
        return $this->createQueryBuilder('v')
            ->innerJoin(Encaissement::class, 'e', join::WITH, 'v.encaissement = e.id')
            ->innerJoin(Journal::class, 'j', join::WITH, 'j.id = e.journal')
            ->innerJoin(Caisse::class, 'c', join::WITH, 'e.id = c.id')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Ventes[] Returns an array of Ventes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ventes
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
