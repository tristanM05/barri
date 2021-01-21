<?php

namespace App\Repository;

use App\Entity\Article;
use App\Service\FilterArticleService;
use App\Service\SearchArticleService;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator ){
        parent::__construct($registry, Article::class);
        $this->paginator = $paginator;
    }

    /**
     * Undocumented function
     * @return PaginationInterface
     */
    public function findSearch(FilterArticleService $search, $client) :PaginationInterface{
        $query = $this
            ->createQueryBuilder('p')
            ->select('s','p')
            ->join('p.productStatus','s')
            ->where('p.client = :client')
            ->orderBy('p.id','DESC')
            ->setParameter('client', $client);

        if (!empty($search->isvisible)) {
            $query = $query
            ->andWhere('p.isvisible = 1');
        }

        // if(!empty($search->q)){
        //     $query = $query
        //     ->andWhere('MATCH_AGAINST(p.designation) AGAINST(:q boolean)>0')
        //     ->setParameter('q', $search->q);
        // }

        if($search->q){
            $queries = explode(" ", $search->q);
            foreach($queries as $searchName){
                $query = $query
                ->andWhere('p.designation LIKE :q')
                ->setParameter('q', '%'.$searchName.'%');
            }
        }
        
        if(!empty($search->n)){
            $query = $query
            ->andWhere('p.number LIKE :n')
            ->setParameter('n', $search->n);
        }

        
        if (!empty($search->min)) {
            $query = $query 
                ->andWhere('p.referenceprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max)) {
            $query = $query 
                ->andWhere('p.referenceprice <= :max')
                ->setParameter('max', $search->max);
        }

        // if (!empty($search->subfamilies)) {
        //     $query = $query 
        //         ->andWhere('c.id IN (:subfamilies)')
        //         ->setParameter('subfamilies', $search->subfamilies);
        // }

        if (!empty($search->status)) {
            $query = $query 
                ->andWhere('s.id IN (:status)')
                ->setParameter('status', $search->status);
        }

        // if (!empty($search->expired)) {
        //     $query = $query 
        //         ->andWhere('p.endDate < :expired')
        //         ->setParameter('expired', $search->expired);
        // }

        $query = $query->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );

    }

    // php repo
    // custom function dans ton repo de articles
    public function findLike($query){
        return $this->createQueryBuilder('a')
        ->andWhere('a.title LIKE :val')
        ->setParameter('val', $query)
        ->orderBy('a.id', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
    }


    // /**
    //  * @return PaginationInterface
    //  */
    // public function findArticle(SearchArticleService $search, $client): PaginationInterface{

    //     if(!empty($search->q)){
    //         $query = $this
    //         ->createQueryBuilder('a')
    //         ->select('a')
    //         ->andWhere('a.designation LIKE :q')
    //         ->andWhere('a.client = :client')
    //         ->andWhere('a.productStatus != 1')
    //         ->setParameter('client', $client)
    //         ->setParameter('q', "%{$search->q}%");

            
            
    //     }

    //     if(empty($query)){
    //         $query = $this
    //         ->createQueryBuilder('a')
    //         ->select('a')
    //         ->andWhere('a.designation LIKE :q')
    //         ->andWhere('a.client = :client')
    //         ->andWhere('a.productStatus = 0')
    //         ->setParameter('client', $client)
    //         ->setParameter('q', "%{$search->q}%");
    //     }
    //     $query = $query->getQuery();

    //     return $this->paginator->paginate(
    //             $query,
    //             1,
    //             2
    //         );
    // }

    // public function findValueOfStock($client){
    //     return $this->createQueryBuilder('m')
    //                 ->innerJoin(ProductStatus::class,'p','WITH','p.id = m.productStatus')
    //                 ->addSelect('SUM(m.referenceprice) as SUM')
    //                 ->where('m.client = :idclient')
    //                 ->andWhere('m.isvisible = :visible')
    //                 ->andWhere('p.wording = :status')
    //                 ->setParameter('idclient' , $client)
    //                 ->setParameter('status' , 'en vente')
    //                 ->setParameter('visible' , 1)
    //                 ->getQuery()
    //                 ->getResult();
    // }

    // public function findValueOfStockBySalepoint($client){
    //     return $this->createQueryBuilder('m')
    //                 ->innerJoin(ProductStatus::class,'p','WITH','p.id = m.productStatus')
    //                 ->addSelect('SUM(m.referenceprice) as value_stock')
    //                 ->addSelect('m')
    //                 ->where('m.client = :idclient')
    //                 ->andWhere('m.isvisible = :visible')
    //                 ->andWhere('p.wording = :status')
    //                 ->groupBy('m.salepoint')
    //                 ->setParameter('idclient' , $client)
    //                 ->setParameter('status' , 'en vente')
    //                 ->setParameter('visible' , 1)
    //                 ->getQuery()
    //                 ->getResult();
    // }

    // public function findNumberProducedByDate($client, $start_date, $end_date){

        
    //     return $this->createQueryBuilder('m')
    //                 ->select('COUNT(m.id) as number')
    //                 ->where('m.client = :idclient')
    //                 ->andWhere('m.productiondate >= :start_date' )
    //                 ->andWhere('m.productiondate <= :end_date' )
    //                 ->andWhere('m.isvisible = :visible')
    //                 ->setParameter('idclient' , $client)
    //                 ->setParameter('start_date' , $start_date)
    //                 ->setParameter('end_date' , $end_date)
    //                 ->setParameter('visible' , 1)
    //                 ->getQuery()
    //                 ->getResult();
    // }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
