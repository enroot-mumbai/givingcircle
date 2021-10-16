<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnAuthOtp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnAuthOtp|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnAuthOtp|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnAuthOtp[]    findAll()
 * @method TrnAuthOtp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnAuthOtpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnAuthOtp::class);
    }

    // /**
    //  * @return TrnAuthOtp[] Returns an array of TrnAuthOtp objects
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
    public function findOneBySomeField($value): ?TrnAuthOtp
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
