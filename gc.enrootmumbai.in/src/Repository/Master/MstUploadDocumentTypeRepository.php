<?php

namespace App\Repository\Master;

use App\Entity\Master\MstUploadDocumentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstUploadDocumentType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstUploadDocumentType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstUploadDocumentType[]    findAll()
 * @method MstUploadDocumentType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstUploadDocumentTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstUploadDocumentType::class);
    }

    // /**
    //  * @return MstUploadDocumentType[] Returns an array of MstUploadDocumentType objects
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
    public function findOneBySomeField($value): ?MstUploadDocumentType
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
