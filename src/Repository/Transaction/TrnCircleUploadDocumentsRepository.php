<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleUploadDocuments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleUploadDocuments|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleUploadDocuments|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleUploadDocuments[]    findAll()
 * @method TrnCircleUploadDocuments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleUploadDocumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleUploadDocuments::class);
    }

    // /**
    //  * @return TrnCircleUploadDocuments[] Returns an array of TrnCircleUploadDocuments objects
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
    public function findOneBySomeField($value): ?TrnCircleUploadDocuments
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
