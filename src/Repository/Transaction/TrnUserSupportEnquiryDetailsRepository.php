<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnUserSupportEnquiryDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnUserSupportEnquiryDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnUserSupportEnquiryDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnUserSupportEnquiryDetails[]    findAll()
 * @method TrnUserSupportEnquiryDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnUserSupportEnquiryDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnUserSupportEnquiryDetails::class);
    }

    // /**
    //  * @return TrnUserSupportEnquiryDetails[] Returns an array of TrnUserSupportEnquiryDetails objects
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
    public function findOneBySomeField($value): ?TrnUserSupportEnquiryDetails
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
