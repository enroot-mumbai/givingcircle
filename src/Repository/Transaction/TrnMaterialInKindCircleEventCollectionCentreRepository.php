<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnMaterialInKindCircleEventCollectionCentre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnMaterialInKindCircleEventCollectionCentre|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnMaterialInKindCircleEventCollectionCentre|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnMaterialInKindCircleEventCollectionCentre[]    findAll()
 * @method TrnMaterialInKindCircleEventCollectionCentre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnMaterialInKindCircleEventCollectionCentreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnMaterialInKindCircleEventCollectionCentre::class);
    }

    // /**
    //  * @return TrnMaterialInKindCircleEventCollectionCentre[] Returns an array of TrnMaterialInKindCircleEventCollectionCentre objects
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
    public function findOneBySomeField($value): ?TrnMaterialInKindCircleEventCollectionCentre
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
