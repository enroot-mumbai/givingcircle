<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterCircleEventVolunterWork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterCircleEventVolunterWork|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterCircleEventVolunterWork|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterCircleEventVolunterWork[]    findAll()
 * @method TrnVolunterCircleEventVolunterWork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterCircleEventVolunterWorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterCircleEventVolunterWork::class);
    }

    // /**
    //  * @return TrnVolunterCircleEventVolunterWork[] Returns an array of TrnVolunterCircleEventVolunterWork objects
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
    public function findOneBySomeField($value): ?TrnVolunterCircleEventVolunterWork
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
