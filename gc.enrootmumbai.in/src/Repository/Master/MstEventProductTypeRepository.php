<?php

namespace App\Repository\Master;

use App\Entity\Master\MstEventProductType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstEventProductType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstEventProductType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstEventProductType[]    findAll()
 * @method MstEventProductType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstEventProductTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstEventProductType::class);
    }

    public function getSelectedProductType($eventProductTypeArr) {

        $query = $this->createQueryBuilder('m')
            ->andWhere('m.id IN (:valArr)')
            ->andWhere('m.isActive = :active')
            ->setParameter('valArr', $eventProductTypeArr)
            ->setParameter('active', 1)
            ->getQuery();

        return $query->getResult();
    }

    // /**
    //  * @return MstEventProductType[] Returns an array of MstEventProductType objects
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
    public function findOneBySomeField($value): ?MstEventProductType
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
