<?php

namespace App\Repository\Master;

use App\Entity\Master\MstStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstStatus[]    findAll()
 * @method MstStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstStatus::class);
    }

    // /**
    //  * @return MstStatus[] Returns an array of MstStatus objects
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
    public function findOneBySomeField($value): ?MstStatus
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
