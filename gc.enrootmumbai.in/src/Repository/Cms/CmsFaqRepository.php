<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsFaq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsFaq|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsFaq|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsFaq[]    findAll()
 * @method CmsFaq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsFaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsFaq::class);
    }

    // /**
    //  * @return MstFaq[] Returns an array of MstFaq objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MstFaq
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
