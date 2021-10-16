<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventUploadedDocuments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventUploadedDocuments|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventUploadedDocuments|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventUploadedDocuments[]    findAll()
 * @method TrnCircleEventUploadedDocuments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventUploadedDocumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventUploadedDocuments::class);
    }

    // /**
    //  * @return TrnCircleEventUploadedDocuments[] Returns an array of TrnCircleEventUploadedDocuments objects
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
    public function findOneBySomeField($value): ?TrnCircleEventUploadedDocuments
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
