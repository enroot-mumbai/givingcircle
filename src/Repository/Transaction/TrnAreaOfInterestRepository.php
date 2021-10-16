<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnAreaOfInterest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnAreaOfInterest|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnAreaOfInterest|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnAreaOfInterest[]    findAll()
 * @method TrnAreaOfInterest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnAreaOfInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnAreaOfInterest::class);
    }

    // /**
    //  * @return TrnAreaOfInterest[] Returns an array of TrnAreaOfInterest objects
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
    public function findOneBySomeField($value): ?TrnAreaOfInterest
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
