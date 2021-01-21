<?php

namespace App\Repository;

use App\Entity\StockClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StockClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockClient[]    findAll()
 * @method StockClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockClient::class);
    }

    // /**
    //  * @return StockClient[] Returns an array of StockClient objects
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
    public function findOneBySomeField($value): ?StockClient
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
