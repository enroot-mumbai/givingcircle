<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCommentsOnCircleEventGoodnessTimeline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCommentsOnCircleEventGoodnessTimeline|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCommentsOnCircleEventGoodnessTimeline|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCommentsOnCircleEventGoodnessTimeline[]    findAll()
 * @method TrnCommentsOnCircleEventGoodnessTimeline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCommentsOnCircleEventGoodnessTimelineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCommentsOnCircleEventGoodnessTimeline::class);
    }

    // /**
    //  * @return TrnCommentsOnCircleEventGoodnessTimeline[] Returns an array of TrnCommentsOnCircleEventGoodnessTimeline objects
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
    public function findOneBySomeField($value): ?TrnCommentsOnCircleEventGoodnessTimeline
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
