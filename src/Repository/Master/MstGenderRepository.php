<?php

namespace App\Repository\Master;

use App\Entity\Master\MstGender;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstGender|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstGender|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstGender[]    findAll()
 * @method MstGender[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstGenderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstGender::class);
    }

    // /**
    //  * @return MstGender[] Returns an array of MstGender objects
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
    public function findOneBySomeField($value): ?MstGender
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
