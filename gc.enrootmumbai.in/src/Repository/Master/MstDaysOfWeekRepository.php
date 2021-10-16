<?php

namespace App\Repository\Master;

use App\Entity\Master\MstDaysOfWeek;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstDaysOfWeek|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstDaysOfWeek|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstDaysOfWeek[]    findAll()
 * @method MstDaysOfWeek[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstDaysOfWeekRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstDaysOfWeek::class);
    }

    // /**
    //  * @return MstDaysOfWeek[] Returns an array of MstDaysOfWeek objects
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
    public function findOneBySomeField($value): ?MstDaysOfWeek
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
