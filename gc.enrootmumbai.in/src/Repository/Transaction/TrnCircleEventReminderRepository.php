<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventReminder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventReminder|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventReminder|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventReminder[]    findAll()
 * @method TrnCircleEventReminder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventReminderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventReminder::class);
    }

    // /**
    //  * @return TrnCircleEventReminder[] Returns an array of TrnCircleEventReminder objects
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
    public function findOneBySomeField($value): ?TrnCircleEventReminder
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
