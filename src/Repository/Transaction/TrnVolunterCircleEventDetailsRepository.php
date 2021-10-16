<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterCircleEventDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterCircleEventDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterCircleEventDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterCircleEventDetails[]    findAll()
 * @method TrnVolunterCircleEventDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterCircleEventDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterCircleEventDetails::class);
    }

    // /**
    //  * @return TrnVolunterCircleEventDetails[] Returns an array of TrnVolunterCircleEventDetails objects
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
    public function findOneBySomeField($value): ?TrnVolunterCircleEventDetails
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
