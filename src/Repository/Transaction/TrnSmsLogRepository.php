<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnSmsLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnSmsLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnSmsLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnSmsLog[]    findAll()
 * @method TrnSmsLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnSmsLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnSmsLog::class);
    }

    // /**
    //  * @return TrnSmsLog[] Returns an array of TrnSmsLog objects
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
    public function findOneBySomeField($value): ?TrnSmsLog
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
