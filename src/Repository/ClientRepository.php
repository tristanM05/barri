<?php

namespace App\Repository;

use App\Entity\Client;
use App\Service\FilterClientService;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Client::class);
        $this->paginator = $paginator;
    }

    /**
     * Undocumented function
     * @return PaginationInterface
     */
    public function findSearch(FilterClientService $search) :PaginationInterface{
        $query = $this
            ->createQueryBuilder('c')
            ->select('c')
            ->where('c.roles != :role')
            ->setParameter('role', 'ROLE_ADMIN');

        if(!empty($search->q)){
            $query = $query
            ->andWhere('c.email LIKE :q')
            ->setParameter('q', "%{$search->q}%");
        }

        $query = $query->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }

    // /**
    //  * @return Client[] Returns an array of Client objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
