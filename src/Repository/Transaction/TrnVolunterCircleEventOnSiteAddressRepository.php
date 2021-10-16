<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterCircleEventOnSiteAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterCircleEventOnSiteAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterCircleEventOnSiteAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterCircleEventOnSiteAddress[]    findAll()
 * @method TrnVolunterCircleEventOnSiteAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterCircleEventOnSiteAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterCircleEventOnSiteAddress::class);
    }

    // /**
    //  * @return TrnVolunterCircleEventOnSiteAddress[] Returns an array of TrnVolunterCircleEventOnSiteAddress objects
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
    public function findOneBySomeField($value): ?TrnVolunterCircleEventOnSiteAddress
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
