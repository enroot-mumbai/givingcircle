<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method CmsArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsArticle[]    findAll()
 * @method CmsArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsArticle::class);
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

    public function getAreaInterestList($category_id, $company_id, $isPrimaryAI = false)
    {
        $sql = $this->createQueryBuilder('a')
            ->select('i.id', 'i.areaInterest')
            ->leftJoin('a.mstAreaInterest', 'i')
            ->andWhere('a.mstArticleCategory =:category')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('a.isActive =:active')
            ->setParameter('category', $category_id)
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->groupBy('i.id')
            ->orderBy('i.areaInterest', 'ASC');

        if($isPrimaryAI == true) {
            $sql->andWhere('i.mstAreaInterestPrimary is NULL');
        }

        $query = $sql->getQuery();
        return $query->getResult();
    }

    public function getArticleByInterestId($interest_id, $company_id)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.mstAreaInterest', 'i')
            ->andWhere('i.id in (:interest) ')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('a.isActive =:active')
            ->setParameter('interest', $interest_id)
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->orderBy('a.articleCreateDateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getChangeMakerBySearchParam($searchParam, $company_id)
    {

        return $this->createQueryBuilder('a')
            ->where('a.articleFor LIKE :searchparam')
            ->orWhere('a.locationName LIKE :searchparam')
            ->orWhere('a.cityName LIKE :searchparam')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('a.isActive =:active')
            ->setParameter('searchparam', '%'.$searchParam.'%')
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->getQuery()
            ->getResult();
    }

    public function getChangeMakerBySearchParamAndInterest($searchParam, $interest_id, $company_id)
    {
        $sql = $this->createQueryBuilder('a')
            ->leftJoin('a.mstAreaInterest', 'i')
            ->where('a.articleFor LIKE :searchparam OR a.locationName LIKE :searchparam OR a.cityName LIKE :searchparam')
            ->andWhere('i.id IN (:interest)')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('a.isActive =:active')
            ->setParameter('searchparam', '%'.$searchParam.'%')
            ->setParameter('interest', $interest_id)
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->getQuery();

        return $sql->getResult();
    }

    public function getBlogBySearchParam($textSearch, $dateSearch, $company_id)
    {

        $sql = $this->createQueryBuilder('a')
            ->where('a.orgCompany =:company')
            ->andWhere('a.mstArticleCategory = :ac')
            ->andWhere('a.isActive =:active')
            ->setParameter('company', $company_id)
            ->setParameter('ac', 1)
            ->setParameter('active', 1)
            ->orderBy('a.articleCreateDateTime', 'DESC');

            if($textSearch != '') {
                $sql->andWhere('a.articleTitle LIKE :searchparam')
                    ->setParameter('searchparam', '%'.$textSearch.'%');
            }
            if($dateSearch != '') {
                $sql->andWhere('a.articleCreateDateTime LIKE :dateparam')
                    ->setParameter('dateparam', $dateSearch);
            }
            return $sql->getQuery()->getResult();
    }

    public function getArticleByCategory($category_id,$company_id)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.mstArticleCategory','ac')
            ->andWhere('ac.id =:category')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('a.isActive =:active')
            ->setParameter('category', $category_id)
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getContentByArticle($page)
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
            $dql = $this->createQueryBuilder('a')
                ->andWhere('a.articleSlugName = :val')
                ->andWhere('a.isActive =:active')
                ->setParameter('val', $slugName)
                ->setParameter('active', 1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
        return $dql;
    }

    public function findOneBySeqNo($value)
    {
        return $this->createQueryBuilder('a')
            ->select('MAX(a.sequenceNo)')
            ->andWhere('a.mstArticleCategory = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getArticleCount($mstCategoryId, $status)
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as articleCnt')
            ->andWhere('c.mstArticleCategory =:category')
            ->andWhere('c.isActive =:active')
            ->setParameter('category', $mstCategoryId)
            ->setParameter('active', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOtherArticlesBySlugName($mstCategoryId, $slugName)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.mstArticleCategory =:category')
            ->andWhere('a.articleSlugName != :val')
            ->andWhere('a.isActive =:active')
            ->setParameter('category', $mstCategoryId)
            ->setParameter('val', $slugName)
            ->setParameter('active', 1)
            ->getQuery()
            ->getResult();
    }

    public function getOtherArticlesBySlugNameLimit($mstCategoryId, $company_id, $article_id, $limit)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.mstArticleCategory =:category')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('a.id != :val')
            ->andWhere('a.isActive =:active')
            ->setParameter('category', $mstCategoryId)
            ->setParameter('val', $article_id)
            ->setParameter('active', 1)
            ->setParameter('company', $company_id)
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->orderBy('a.articleCreateDateTime', 'DESC')
            ->addOrderBy('a.sequenceNo' , 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getYearList($orgCompany_id, $mstCategory)
    {
        $em = $this->getEntityManager();

        $emConfig = $em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        return $this->createQueryBuilder('c')
            ->select("YEAR(c.articleCreateDateTime) as year")
            ->andWhere('c.isActive = :val')
            ->andWhere('c.mstArticleCategory = :category')
            ->andWhere('c.orgCompany = :cmp_id')
            ->setParameter('cmp_id', $orgCompany_id)
            ->setParameter('category', $mstCategory)
            ->setParameter('val', 1)
            ->orderBy('year', 'DESC')
            ->distinct('year')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getMonthList($orgCompany_id, $mstCategory)
    {
        $em = $this->getEntityManager();

        $emConfig = $em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');

        return $this->createQueryBuilder('c')
            ->select("MONTH(c.articleCreateDateTime) as month")
            ->andWhere('c.isActive = :val')
            ->andWhere('c.mstArticleCategory = :category')
            ->andWhere('c.orgCompany = :cmp_id')
            ->setParameter('cmp_id', $orgCompany_id)
            ->setParameter('category', $mstCategory)
            ->setParameter('val', 1)
            ->orderBy('month', 'ASC')
            ->distinct('month')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getChangeMakerByUserId($mstCategory, $user) {

        return $this->createQueryBuilder('a')
            ->andWhere('a.mstArticleCategory =:category')
            ->andWhere('a.changeMakerAppUser = :user')
            ->andWhere('a.isActive =:active')
            ->setParameter('category', $mstCategory)
            ->setParameter('user', $user)
            ->setParameter('active', 1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getChangeMakerByName($searchText, $company_id, $mstCategory)
    {
        return $this->createQueryBuilder('a')
            ->where('a.articleFor LIKE :searchText OR a.articleTitle LIKE :searchText OR a.articleCreator LIKE :searchText')
            ->andWhere('a.mstArticleCategory =:category')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('a.isActive =:active')
            ->setParameter('searchText', '%'.$searchText.'%')
            ->setParameter('category', $mstCategory)
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->getQuery()
            ->getResult();
    }

}
