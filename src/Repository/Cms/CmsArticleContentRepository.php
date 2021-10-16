<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsArticleContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsArticleContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsArticleContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsArticleContent[]    findAll()
 * @method CmsArticleContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsArticleContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsArticleContent::class);
    }
}
