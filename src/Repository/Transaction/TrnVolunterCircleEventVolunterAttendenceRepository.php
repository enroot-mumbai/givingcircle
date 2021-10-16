<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterCircleEventVolunterAttendence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterCircleEventVolunterAttendence|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterCircleEventVolunterAttendence|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterCircleEventVolunterAttendence[]    findAll()
 * @method TrnVolunterCircleEventVolunterAttendence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterCircleEventVolunterAttendenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterCircleEventVolunterAttendence::class);
    }

    // /**
    //  * @return TrnVolunterCircleEventVolunterAttendence[] Returns an array of TrnVolunterCircleEventVolunterAttendence objects
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
    public function findOneBySomeField($value): ?TrnVolunterCircleEventVolunterAttendence
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
