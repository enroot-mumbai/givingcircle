<?php

namespace App\Repository\Master;

use App\Entity\Master\MstPaymentGateway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstPaymentGateway|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstPaymentGateway|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstPaymentGateway[]    findAll()
 * @method MstPaymentGateway[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstPaymentGatewayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstPaymentGateway::class);
    }

    // /**
    //  * @return MstPaymentGateway[] Returns an array of MstPaymentGateway objects
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
    public function findOneBySomeField($value): ?MstPaymentGateway
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
