<?php

namespace App\Repository\Master;

use App\Entity\Master\MstUserQueryType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstUserQueryType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstUserQueryType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstUserQueryType[]    findAll()
 * @method MstUserQueryType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstUserQueryTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstUserQueryType::class);
    }

    // /**
    //  * @return MstUserQueryType[] Returns an array of MstUserQueryType objects
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
    public function findOneBySomeField($value): ?MstUserQueryType
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
