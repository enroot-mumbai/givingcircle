<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnUserPastWork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnUserPastWork|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnUserPastWork|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnUserPastWork[]    findAll()
 * @method TrnUserPastWork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnUserPastWorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnUserPastWork::class);
    }

    // /**
    //  * @return TrnUserPastWork[] Returns an array of TrnUserPastWork objects
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
    public function findOneBySomeField($value): ?TrnUserPastWork
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
