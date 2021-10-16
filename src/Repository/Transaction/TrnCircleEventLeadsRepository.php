<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventLeads;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventLeads|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventLeads|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventLeads[]    findAll()
 * @method TrnCircleEventLeads[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventLeadsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventLeads::class);
    }

    // /**
    //  * @return TrnCircleEventLeads[] Returns an array of TrnCircleEventLeads objects
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
    public function findOneBySomeField($value): ?TrnCircleEventLeads
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
