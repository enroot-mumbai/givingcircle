<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnAppUserContacts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnAppUserContacts|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnAppUserContacts|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnAppUserContacts[]    findAll()
 * @method TrnAppUserContacts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnAppUserContactsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnAppUserContacts::class);
    }

    // /**
    //  * @return TrnAppUserContacts[] Returns an array of TrnAppUserContacts objects
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
    public function findOneBySomeField($value): ?TrnAppUserContacts
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
