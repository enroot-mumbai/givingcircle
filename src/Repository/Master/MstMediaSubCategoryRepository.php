<?php

namespace App\Repository\Master;

use App\Entity\Master\MstMediaSubCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstMediaSubCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstMediaSubCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstMediaSubCategory[]    findAll()
 * @method MstMediaSubCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstMediaSubCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstMediaSubCategory::class);
    }

    public function getSubCategoryByCategoryId($category_id)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.mediaSubCategory')
            ->andWhere('c.mediaCategory = :category')
            ->andWhere('c.isActive = :active')
            ->setParameter('category', $category_id)
            ->setParameter('active', 1)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySeqNo()
    {
        return $this->createQueryBuilder('m')
            ->select('MAX(m.sequenceNo)')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
