<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsBanner;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsBanner|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsBanner|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsBanner[]    findAll()
 * @method CmsBanner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsBannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsBanner::class);
    }

    public function getBanner($page_id)
    {
        $date = new DateTime('now');
        return $this->createQueryBuilder('b')
            ->andWhere('b.cmsPage =:page')
            ->andWhere('b.isActive =:active')
            ->andWhere(':now between b.bannerValidFromDate AND b.bannerValidToDate')
            ->setParameter('page', $page_id)
            ->setParameter('active', 1)
            ->setParameter('now', $date->format('Y-m-d'))
            ->orderBy('b.sequenceNo', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findOneBySeqNo($value)
    {
        try {
            return $this->createQueryBuilder('b')
                ->select('MAX(b.sequenceNo)')
                ->andWhere('b.cmsPage = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
    }

}
