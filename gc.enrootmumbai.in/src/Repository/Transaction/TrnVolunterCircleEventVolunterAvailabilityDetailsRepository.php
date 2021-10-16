<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterCircleEventVolunterAvailabilityDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterCircleEventVolunterAvailabilityDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterCircleEventVolunterAvailabilityDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterCircleEventVolunterAvailabilityDetails[]    findAll()
 * @method TrnVolunterCircleEventVolunterAvailabilityDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterCircleEventVolunterAvailabilityDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterCircleEventVolunterAvailabilityDetails::class);
    }

    // /**
    //  * @return TrnVolunterCircleEventVolunterAvailabilityDetails[] Returns an array of TrnVolunterCircleEventVolunterAvailabilityDetails objects
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
    public function findOneBySomeField($value): ?TrnVolunterCircleEventVolunterAvailabilityDetails
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
