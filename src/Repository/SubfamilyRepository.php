<?php

namespace App\Repository;

use App\Entity\Subfamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Subfamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subfamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subfamily[]    findAll()
 * @method Subfamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubfamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subfamily::class);
    }

    // /**
    //  * @return Subfamily[] Returns an array of Subfamily objects
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
    public function findOneBySomeField($value): ?Subfamily
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
