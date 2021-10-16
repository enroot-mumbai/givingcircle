<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnMemberContactList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnMemberContactList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnMemberContactList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnMemberContactList[]    findAll()
 * @method TrnMemberContactList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnMemberContactListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnMemberContactList::class);
    }

    // /**
    //  * @return TrnMemberContactList[] Returns an array of TrnMemberContactList objects
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
    public function findOneBySomeField($value): ?TrnMemberContactList
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
