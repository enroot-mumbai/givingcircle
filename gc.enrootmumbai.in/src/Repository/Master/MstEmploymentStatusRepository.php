<?php

namespace App\Repository\Master;

use App\Entity\Master\MstEmploymentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstEmploymentStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstEmploymentStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstEmploymentStatus[]    findAll()
 * @method MstEmploymentStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstEmploymentStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstEmploymentStatus::class);
    }

    // /**
    //  * @return MstEmploymentStatus[] Returns an array of MstEmploymentStatus objects
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
    public function findOneBySomeField($value): ?MstEmploymentStatus
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
