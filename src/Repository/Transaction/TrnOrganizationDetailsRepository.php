<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnOrganizationDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnOrganizationDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnOrganizationDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnOrganizationDetails[]    findAll()
 * @method TrnOrganizationDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnOrganizationDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnOrganizationDetails::class);
    }

    // /**
    //  * @return TrnOrganizationDetails[] Returns an array of TrnOrganizationDetails objects
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
    public function findOneBySomeField($value): ?TrnOrganizationDetails
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
