<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnProductMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnProductMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnProductMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnProductMedia[]    findAll()
 * @method TrnProductMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnProductMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnProductMedia::class);
    }

    // /**
    //  * @return TrnProductMedia[] Returns an array of TrnProductMedia objects
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
    public function findOneBySomeField($value): ?TrnProductMedia
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findImgByFilename($value): ?TrnProductMedia
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.mediaFileName = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
