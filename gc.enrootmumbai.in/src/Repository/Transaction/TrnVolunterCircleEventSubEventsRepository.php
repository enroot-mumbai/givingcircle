<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterCircleEventSubEvents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterCircleEventSubEvents|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterCircleEventSubEvents|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterCircleEventSubEvents[]    findAll()
 * @method TrnVolunterCircleEventSubEvents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterCircleEventSubEventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterCircleEventSubEvents::class);
    }

    // /**
    //  * @return TrnVolunterCircleEventSubEvents[] Returns an array of TrnVolunterCircleEventSubEvents objects
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
    public function findOneBySomeField($value): ?TrnVolunterCircleEventSubEvents
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
