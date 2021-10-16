<?php

namespace App\Repository\Master;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstCity|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstCity|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstCity[]    findAll()
 * @method MstCity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstCityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstCity::class);
    }

    public function getCityListByStateId($state_id)
    {
        $dql = $this->createQueryBuilder('c')
            ->select('c.id', 'c.city as name')
            ->where('c.mstState = :val')
            ->setParameter('val', $state_id)
            ->getQuery()
            ->getResult()
        ;
        return $dql;

    }

    public function getCityListByCountryId($value, $country_id)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.city LIKE :city')
            ->andWhere('c.mstCountry = :country')
            ->setParameter('city', '%'.$value.'%')
            ->setParameter('country', $country_id)
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;

    }

}
