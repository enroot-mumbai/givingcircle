<?php

namespace App\Repository\Master;

use App\Entity\Master\MstEventOccurrence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstEventOccurrence|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstEventOccurrence|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstEventOccurrence[]    findAll()
 * @method MstEventOccurrence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstEventOccurrenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstEventOccurrence::class);
    }

    // /**
    //  * @return MstEventOccurrence[] Returns an array of MstEventOccurrence objects
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
    public function findOneBySomeField($value): ?MstEventOccurrence
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
