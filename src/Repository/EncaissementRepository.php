<?php

namespace App\Repository;

use App\Entity\Caisse;
use App\Data\SearchData;
use App\Entity\Encaissement;
use App\Entity\Journal;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\WhereClause;

/**
 * @method Encaissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Encaissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Encaissement[]    findAll()
 * @method Encaissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncaissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Encaissement::class);
    }

    public function findEncFromCaisse($journal, $idCaisse)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin(Caisse::class, 'c', join::WITH, 'e.id = :caisse')
            ->where('e.journal = :journal')
            ->setParameter('journal', $journal)
            ->setParameter('caisse', $idCaisse)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Encaissement[] Returns an array of Encaissement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Encaissement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
