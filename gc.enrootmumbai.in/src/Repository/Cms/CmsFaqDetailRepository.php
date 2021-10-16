<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsFaqDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsFaqDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsFaqDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsFaqDetail[]    findAll()
 * @method CmsFaqDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsFaqDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsFaqDetail::class);
    }

    public function findOneBySeqNo($value)
    {
        try {
            return $this->createQueryBuilder('f')
                ->select('MAX(f.sequenceNo)')
                ->andWhere('f.cmsFaq = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
    }

}
