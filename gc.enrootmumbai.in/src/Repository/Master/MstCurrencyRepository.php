<?php

namespace App\Repository\Master;

use App\Entity\Master\MstCurrency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstCurrency|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstCurrency|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstCurrency[]    findAll()
 * @method MstCurrency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstCurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstCurrency::class);
    }

    public function findAllActive()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.isActive = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getResult()
            ;
    }

//     /**
//      * @return MstCurrency[] Returns an array of MstCurrency objects
//      */
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
    public function findOneBySomeField($value): ?MstCurrency
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
