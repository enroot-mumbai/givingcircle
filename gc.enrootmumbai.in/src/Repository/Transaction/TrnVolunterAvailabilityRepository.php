<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterAvailability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterAvailability|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterAvailability|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterAvailability[]    findAll()
 * @method TrnVolunterAvailability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterAvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterAvailability::class);
    }

    // /**
    //  * @return TrnVolunterAvailability[] Returns an array of TrnVolunterAvailability objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrnVolunterAvailability
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
