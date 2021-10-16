<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnFundRaiserCircleEventSubEvents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnFundRaiserCircleEventSubEvents|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnFundRaiserCircleEventSubEvents|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnFundRaiserCircleEventSubEvents[]    findAll()
 * @method TrnFundRaiserCircleEventSubEvents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnFundRaiserCircleEventSubEventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnFundRaiserCircleEventSubEvents::class);
    }

    // /**
    //  * @return TrnFundRaiserCircleEventSubEvents[] Returns an array of TrnFundRaiserCircleEventSubEvents objects
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
    public function findOneBySomeField($value): ?TrnFundRaiserCircleEventSubEvents
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
