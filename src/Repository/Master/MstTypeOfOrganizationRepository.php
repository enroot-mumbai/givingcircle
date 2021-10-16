<?php

namespace App\Repository\Master;

use App\Entity\Master\MstTypeOfOrganization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstTypeOfOrganization|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstTypeOfOrganization|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstTypeOfOrganization[]    findAll()
 * @method MstTypeOfOrganization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstTypeOfOrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstTypeOfOrganization::class);
    }

    // /**
    //  * @return MstTypeOfOrganization[] Returns an array of MstTypeOfOrganization objects
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
    public function findOneBySomeField($value): ?MstTypeOfOrganization
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
