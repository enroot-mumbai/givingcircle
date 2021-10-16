<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventInvitations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventInvitations|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventInvitations|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventInvitations[]    findAll()
 * @method TrnCircleEventInvitations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventInvitationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventInvitations::class);
    }

    // /**
    //  * @return TrnCircleEventInvitations[] Returns an array of TrnCircleEventInvitations objects
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
    public function findOneBySomeField($value): ?TrnCircleEventInvitations
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
