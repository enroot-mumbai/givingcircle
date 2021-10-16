<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsPressRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method CmsPressRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsPressRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsPressRoom[]    findAll()
 * @method CmsPressRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsPressRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsPressRoom::class);
    }

    public function getActiveNewsCount()
    {
        return $this->createQueryBuilder('n')
            ->select("count(1) as newsCount")
            ->andWhere('n.isActive = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getPressRelease()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isActive = :val')
            ->setParameter('val', 1)
            ->orderBy('c.articleDateTime', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getYearList($orgCompany_id)
    {
        $em = $this->getEntityManager();

        $emConfig = $em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        return $this->createQueryBuilder('c')
            ->select("YEAR(c.articleDateTime) as year")
            ->andWhere('c.isActive = :val')
            ->andWhere('c.orgCompany = :cmp_id')
            ->setParameter('cmp_id', $orgCompany_id)
            ->setParameter('val', 1)
            ->orderBy('year', 'ASC')
            ->distinct('year')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getMonthList($orgCompany_id)
    {
        $em = $this->getEntityManager();

        $emConfig = $em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');

        return $this->createQueryBuilder('c')
            ->select("MONTH(c.articleDateTime) as month")
            ->andWhere('c.isActive = :val')
            ->andWhere('c.orgCompany = :cmp_id')
            ->setParameter('cmp_id', $orgCompany_id)
            ->setParameter('val', 1)
            ->orderBy('month', 'ASC')
            ->distinct('month')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getNewsBySearchParam($textSearch, $dateSearch, $company_id)
    {

        $query = $this->createQueryBuilder('a')
            ->where('a.orgCompany =:company')
            ->andWhere('a.isActive =:active')
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->orderBy('a.articleDateTime', 'DESC');

        if($textSearch != '') {
            $query->andWhere('a.articleHeading LIKE :searchparam')
                ->setParameter('searchparam', '%'.$textSearch.'%');
        }
        if($dateSearch != '') {
            $query->andWhere('a.articleDateTime LIKE :dateparam')
                ->setParameter('dateparam', $dateSearch);
        }

        return $query->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?CmsPressRoom
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
