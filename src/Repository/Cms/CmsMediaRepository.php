<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsMedia[]    findAll()
 * @method CmsMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsMedia::class);
    }

    public function findOneBySeqNo()
    {
        return $this->createQueryBuilder('m')
            ->select('MAX(m.sequenceNo)')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
