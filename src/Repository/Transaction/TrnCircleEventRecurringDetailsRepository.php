<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventRecurringDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventRecurringDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventRecurringDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventRecurringDetails[]    findAll()
 * @method TrnCircleEventRecurringDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventRecurringDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventRecurringDetails::class);
    }

    // /**
    //  * @return TrnCircleEventRecurringDetails[] Returns an array of TrnCircleEventRecurringDetails objects
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
    public function findOneBySomeField($value): ?TrnCircleEventRecurringDetails
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
