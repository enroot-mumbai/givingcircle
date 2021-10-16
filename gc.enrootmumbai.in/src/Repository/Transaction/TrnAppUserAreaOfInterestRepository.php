<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnAppUserAreaOfInterest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnAppUserAreaOfInterest|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnAppUserAreaOfInterest|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnAppUserAreaOfInterest[]    findAll()
 * @method TrnAppUserAreaOfInterest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnAppUserAreaOfInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnAppUserAreaOfInterest::class);
    }

    // /**
    //  * @return TrnAppUserAreaOfInterest[] Returns an array of TrnAppUserAreaOfInterest objects
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
    public function findOneBySomeField($value): ?TrnAppUserAreaOfInterest
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
