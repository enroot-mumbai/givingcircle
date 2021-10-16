<?php

namespace App\Repository\Master;

use App\Entity\Master\MstMediaCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstMediaCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstMediaCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstMediaCategory[]    findAll()
 * @method MstMediaCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstMediaCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstMediaCategory::class);
    }

    public function findOneBySeqNo()
    {
        return $this->createQueryBuilder('m')
            ->select('MAX(m.sequenceNo)')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
