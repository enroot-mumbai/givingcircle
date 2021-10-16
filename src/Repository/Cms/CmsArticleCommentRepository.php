<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsArticleComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsArticleComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsArticleComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsArticleComment[]    findAll()
 * @method CmsArticleComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsArticleCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsArticleComment::class);
    }

    public function updateStatus($comment_id, $status)
    {
        if ($status == 'approve') {
            $id = 1;
        }
        if ($status == 'unapprove') {
            $id = 0;
        }
        $update = $this->createQueryBuilder('c')
            ->update()
            ->set('c.isApproved', ':status')
            ->setParameter('status', $id)
            ->andWhere('c.id =:id')
            ->setParameter('id', $comment_id)
            ->getQuery()
            ->execute();

        return $update;
    }

    public function getArticleCommentCount($article_id, $is_approved)
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as comment')
            ->andWhere('c.cmsArticle =:article')
            ->andWhere('c.isApproved =:approve')
            ->setParameter('article', $article_id)
            ->setParameter('approve', $is_approved)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
