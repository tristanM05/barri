<?php

namespace App\Repository;

use App\Entity\Journal;
use App\Data\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Journal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Journal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Journal[]    findAll()
 * @method Journal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Journal::class);
    }

    /**
     * get jounal by day
     *
     * @param [type] $value
     */
    public function findSearchEnc(SearchData $search, $client)
    {
        $query = $this
            ->createQueryBuilder('e')
            ->select('e')
            ->where('e.client = :client')
            ->setParameter('client', $client);

            if (!empty($search->date)) {
                $query = $query 
                    ->andWhere('e.date = :date')
                    ->setParameter('date', $search->date);
            }
        return $query->getQuery()->getResult();
    }
    // /**
    //  * @return Journal[] Returns an array of Journal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Journal
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
