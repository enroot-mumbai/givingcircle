<?php

namespace App\Repository\Master;

use App\Entity\Master\MstAreasInCity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstAreasInCity|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstAreasInCity|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstAreasInCity[]    findAll()
 * @method MstAreasInCity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstAreasInCityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstAreasInCity::class);
    }

    public function getAreaInCityListByCityId($city_id)
    {
        $dql = $this->createQueryBuilder('c')
            ->select('c.id', 'c.area as name')
            ->where('c.mstCity = :val')
            ->setParameter('val', $city_id)
            ->getQuery()
            ->getResult()
        ;
        return $dql;

    }

    // /**
    //  * @return MstAreasInCity[] Returns an array of MstAreasInCity objects
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
    public function findOneBySomeField($value): ?MstAreasInCity
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
