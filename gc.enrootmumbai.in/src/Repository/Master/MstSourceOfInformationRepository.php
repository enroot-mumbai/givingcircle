<?php

namespace App\Repository\Master;

use App\Entity\Master\MstSourceOfInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstSourceOfInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstSourceOfInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstSourceOfInformation[]    findAll()
 * @method MstSourceOfInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstSourceOfInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstSourceOfInformation::class);
    }

    // /**
    //  * @return MstSourceOfInformation[] Returns an array of MstSourceOfInformation objects
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
    public function findOneBySomeField($value): ?MstSourceOfInformation
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
