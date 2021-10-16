<?php

namespace App\Repository\Master;

use App\Entity\Master\MstHighlights;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstHighlights|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstHighlights|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstHighlights[]    findAll()
 * @method MstHighlights[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstHighlightsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstHighlights::class);
    }

    // /**
    //  * @return MstHighlights[] Returns an array of MstHighlights objects
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
    public function findOneBySomeField($value): ?MstHighlights
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
