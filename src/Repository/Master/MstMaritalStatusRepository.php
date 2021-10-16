<?php

namespace App\Repository\Master;

use App\Entity\Master\MstMaritalStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstMaritalStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstMaritalStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstMaritalStatus[]    findAll()
 * @method MstMaritalStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstMaritalStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstMaritalStatus::class);
    }

    // /**
    //  * @return MstMaritalStatus[] Returns an array of MstMaritalStatus objects
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
    public function findOneBySomeField($value): ?MstMaritalStatus
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
