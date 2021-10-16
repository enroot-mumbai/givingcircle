<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCrowdFundEventDistributedDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCrowdFundEventDistributedDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCrowdFundEventDistributedDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCrowdFundEventDistributedDetails[]    findAll()
 * @method TrnCrowdFundEventDistributedDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCrowdFundEventDistributedDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCrowdFundEventDistributedDetails::class);
    }

    // /**
    //  * @return TrnCrowdFundEventDistributedDetails[] Returns an array of TrnCrowdFundEventDistributedDetails objects
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
    public function findOneBySomeField($value): ?TrnCrowdFundEventDistributedDetails
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
