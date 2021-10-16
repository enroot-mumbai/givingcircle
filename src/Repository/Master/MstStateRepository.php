<?php

namespace App\Repository\Master;

use App\Entity\Master\MstState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstState|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstState|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstState[]    findAll()
 * @method MstState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstState::class);
    }

    public function getStateListByCountryId($country_id)
    {
        return $this->createQueryBuilder('s')
            ->select('s.id', 's.state as name')
            ->andWhere('s.mstCountry = :val')
            ->setParameter('val', $country_id)
            ->orderBy('name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getStatesList($value, $country_id)
    {
        return $this->createQueryBuilder('s')
            ->select('s.id, s.state as text')
            ->andWhere('s.state LIKE :val')
            ->andWhere('s.mstCountry = :country')
            ->setParameter('val', $value.'%')
            ->setParameter('country', $country_id)
            ->orderBy('s.state', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
    }


}
