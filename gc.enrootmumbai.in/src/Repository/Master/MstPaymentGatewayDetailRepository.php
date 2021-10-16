<?php

namespace App\Repository\Master;

use App\Entity\Master\MstPaymentGatewayDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstPaymentGatewayDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstPaymentGatewayDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstPaymentGatewayDetail[]    findAll()
 * @method MstPaymentGatewayDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstPaymentGatewayDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstPaymentGatewayDetail::class);
    }

    // /**
    //  * @return MstPaymentGatewayDetail[] Returns an array of MstPaymentGatewayDetail objects
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
    public function findOneBySomeField($value): ?MstPaymentGatewayDetail
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
