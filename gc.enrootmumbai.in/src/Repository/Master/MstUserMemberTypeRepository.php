<?php

namespace App\Repository\Master;

use App\Entity\Master\MstUserMemberType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstUserMemberType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstUserMemberType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstUserMemberType[]    findAll()
 * @method MstUserMemberType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstUserMemberTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstUserMemberType::class);
    }

    // /**
    //  * @return MstUserMemberType[] Returns an array of MstUserMemberType objects
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
    public function findOneBySomeField($value): ?MstUserMemberType
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
