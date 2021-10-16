<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventGoodnessTimeline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventGoodnessTimeline|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventGoodnessTimeline|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventGoodnessTimeline[]    findAll()
 * @method TrnCircleEventGoodnessTimeline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventGoodnessTimelineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventGoodnessTimeline::class);
    }

    // /**
    //  * @return TrnCircleEventGoodnessTimeline[] Returns an array of TrnCircleEventGoodnessTimeline objects
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
    public function findOneBySomeField($value): ?TrnCircleEventGoodnessTimeline
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
