<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnOrganizationUploadDocuments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnOrganizationUploadDocuments|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnOrganizationUploadDocuments|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnOrganizationUploadDocuments[]    findAll()
 * @method TrnOrganizationUploadDocuments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnOrganizationUploadDocumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnOrganizationUploadDocuments::class);
    }

    // /**
    //  * @return TrnOrganizationUploadDocuments[] Returns an array of TrnOrganizationUploadDocuments objects
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
    public function findOneBySomeField($value): ?TrnOrganizationUploadDocuments
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
