<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\CmsNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsNotification[]    findAll()
 * @method CmsNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsNotification::class);
    }

    // /**
    //  * @return CmsNotification[] Returns an array of CmsNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CmsNotification
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
