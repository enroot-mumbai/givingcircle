<?php

namespace App\Repository\Master;

use App\Entity\Master\MstBankAccountType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstBankAccountType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstBankAccountType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstBankAccountType[]    findAll()
 * @method MstBankAccountType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstBankAccountTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstBankAccountType::class);
    }


    public function findAllActive()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return MstBankAccountType[] Returns an array of MstBankAccountType objects
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
    public function findOneBySomeField($value): ?MstBankAccountType
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
