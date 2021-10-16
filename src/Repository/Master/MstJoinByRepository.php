<?php

namespace App\Repository\Master;

use App\Entity\Master\MstJoinBy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstJoinBy|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstJoinBy|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstJoinBy[]    findAll()
 * @method MstJoinBy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstJoinByRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstJoinBy::class);
    }

    // /**
    //  * @return MstJoinBy[] Returns an array of MstJoinBy objects
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
    public function findOneBySomeField($value): ?MstJoinBy
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
