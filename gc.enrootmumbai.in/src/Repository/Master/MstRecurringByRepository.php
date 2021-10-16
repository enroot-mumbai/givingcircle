<?php

namespace App\Repository\Master;

use App\Entity\Master\MstRecurringBy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstRecurringBy|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstRecurringBy|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstRecurringBy[]    findAll()
 * @method MstRecurringBy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstRecurringByRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstRecurringBy::class);
    }

    // /**
    //  * @return MstRecurringBy[] Returns an array of MstRecurringBy objects
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
    public function findOneBySomeField($value): ?MstRecurringBy
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
