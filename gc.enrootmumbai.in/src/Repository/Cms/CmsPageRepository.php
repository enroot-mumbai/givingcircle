<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method CmsPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsPage[]    findAll()
 * @method CmsPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsPage::class);
    }

    public function getParentPage()
    {
        $dql =  $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        $sm['None'] = 0;
        foreach ($dql as $value) {

            $sm[$value['pageName']] = $value['id'];
        }

        return $sm;
    }

    public function getContent($page_id)
    {
        try {
            $dql = $this->createQueryBuilder('c')
                ->select('c.pageName', 'c.pageContent')
                ->andWhere('c.id = :val')
                ->setParameter('val', $page_id)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
        return $dql;
    }

    public function getContentByPage($page)
    {
        try {
            $dql = $this->createQueryBuilder('c')
                ->andWhere('c.pageRoute = :val')
                ->andWhere('c.isActive =:active')
                ->setParameter('val', $page)
                ->setParameter('active', 1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
        return $dql;
    }

    public function getContentBySlugName($slugName)
    {
        try {
            $dql = $this->createQueryBuilder('c')
                ->andWhere('c.slugName = :val')
                ->setParameter('val', $slugName)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
        return $dql;
    }

    public function getCmsPage()
    {
        return $this->createQueryBuilder('c')
            ->where('c.pageRoute IS NOT NULL')
            ->andWhere('c.isActive =:active')
            ->setParameter('active', 1)
            ->getQuery()
            ->getResult();
    }

    public function getListByRouteSlug($pageRoute, $pageSlugname, $company_id, $orderByArr = array()) {
        $dql = $this->createQueryBuilder('c')
            ->where('c.pageRoute = :route')
            ->andWhere('c.slugName != :slugname')
            ->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company')
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->setParameter('route', $pageRoute)
            ->setParameter('slugname', $pageSlugname);

        if(!empty($orderByArr)) {
            foreach ($orderByArr as $orderBy) {
                $dql->addOrderBy('c.'.$orderBy['field'], $orderBy['order']);
            }
        }

        return $dql->getQuery()->getResult();
    }

    public function getContentByParent($parent_id)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.parentId = :val')
            ->andWhere('c.isActive = :active')
            ->setParameter('val', $parent_id)
            ->setParameter('active', 1)
            ->getQuery()
            ->getResult();
    }

}
