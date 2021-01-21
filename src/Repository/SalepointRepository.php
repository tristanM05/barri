<?php

namespace App\Repository;

use App\Entity\Salepoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Salepoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Salepoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Salepoint[]    findAll()
 * @method Salepoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalepointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salepoint::class);
    }

    // /**
    //  * @return Salepoint[] Returns an array of Salepoint objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Salepoint
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
