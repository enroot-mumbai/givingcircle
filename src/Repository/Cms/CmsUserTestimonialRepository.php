<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsUserTestimonial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsUserTestimonial|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsUserTestimonial|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsUserTestimonial[]    findAll()
 * @method CmsUserTestimonial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsUserTestimonialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsUserTestimonial::class);
    }

    public function getUserTestimonial()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.createDateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
