<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleInvitations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleInvitations|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleInvitations|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleInvitations[]    findAll()
 * @method TrnCircleInvitations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleInvitationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleInvitations::class);
    }

    // /**
    //  * @return TrnCircleInvitations[] Returns an array of TrnCircleInvitations objects
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
    public function findOneBySomeField($value): ?TrnCircleInvitations
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
