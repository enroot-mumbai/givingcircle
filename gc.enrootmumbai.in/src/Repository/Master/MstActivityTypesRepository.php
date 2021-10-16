<?php

namespace App\Repository\Master;

use App\Entity\Master\MstActivityTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstActivityTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstActivityTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstActivityTypes[]    findAll()
 * @method MstActivityTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstActivityTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstActivityTypes::class);
    }

    // /**
    //  * @return MstActivityTypes[] Returns an array of MstActivityTypes objects
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
    public function findOneBySomeField($value): ?MstActivityTypes
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
