<?php

namespace App\Repository;

use App\Entity\Lots;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lots|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lots|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lots[]    findAll()
 * @method Lots[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LotsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lots::class);
    }

    // /**
    //  * @return Lots[] Returns an array of Lots objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lots
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
