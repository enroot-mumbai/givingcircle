<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterSourceOfInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterSourceOfInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterSourceOfInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterSourceOfInformation[]    findAll()
 * @method TrnVolunterSourceOfInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterSourceOfInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterSourceOfInformation::class);
    }

    // /**
    //  * @return TrnVolunterSourceOfInformation[] Returns an array of TrnVolunterSourceOfInformation objects
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
    public function findOneBySomeField($value): ?TrnVolunterSourceOfInformation
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
