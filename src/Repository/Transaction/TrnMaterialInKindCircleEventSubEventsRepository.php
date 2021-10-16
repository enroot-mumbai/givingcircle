<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnMaterialInKindCircleEventSubEvents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnMaterialInKindCircleEventSubEvents|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnMaterialInKindCircleEventSubEvents|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnMaterialInKindCircleEventSubEvents[]    findAll()
 * @method TrnMaterialInKindCircleEventSubEvents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnMaterialInKindCircleEventSubEventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnMaterialInKindCircleEventSubEvents::class);
    }

    // /**
    //  * @return TrnMaterialInKindCircleEventSubEvents[] Returns an array of TrnMaterialInKindCircleEventSubEvents objects
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
    public function findOneBySomeField($value): ?TrnMaterialInKindCircleEventSubEvents
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
