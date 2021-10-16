<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnMaterialInKindCircleEventDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnMaterialInKindCircleEventDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnMaterialInKindCircleEventDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnMaterialInKindCircleEventDetails[]    findAll()
 * @method TrnMaterialInKindCircleEventDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnMaterialInKindCircleEventDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnMaterialInKindCircleEventDetails::class);
    }

    // /**
    //  * @return TrnMaterialInKindCircleEventDetails[] Returns an array of TrnMaterialInKindCircleEventDetails objects
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
    public function findOneBySomeField($value): ?TrnMaterialInKindCircleEventDetails
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
