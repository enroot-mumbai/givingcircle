<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventsVisitors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventsVisitors|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventsVisitors|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventsVisitors[]    findAll()
 * @method TrnCircleEventsVisitors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventsVisitorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventsVisitors::class);
    }

    // /**
    //  * @return TrnCircleEventsVisitors[] Returns an array of TrnCircleEventsVisitors objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrnCircleEventsVisitors
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
