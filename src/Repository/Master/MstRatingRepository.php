<?php

namespace App\Repository\Master;

use App\Entity\Master\MstRating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstRating[]    findAll()
 * @method MstRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstRating::class);
    }

    public function getRatingList($ratingName)
    {
        $dql =  $this->createQueryBuilder('c')
            ->select('c.id, c.ratingName as text')
            ->andWhere('c.ratingName LIKE :val')
            ->setParameter('val', $ratingName.'%')
            ->orderBy('c.ratingName', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
        return $dql;

    }
    // /**
    //  * @return MstRating[] Returns an array of MstRating objects
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
    public function findOneBySomeField($value): ?MstRating
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
