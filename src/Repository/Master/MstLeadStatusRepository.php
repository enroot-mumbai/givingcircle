<?php

namespace App\Repository\Master;

use App\Entity\Master\MstLeadStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstLeadStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstLeadStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstLeadStatus[]    findAll()
 * @method MstLeadStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstLeadStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstLeadStatus::class);
    }

    // /**
    //  * @return MstLeadStatus[] Returns an array of MstLeadStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MstLeadStatus
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
