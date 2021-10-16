<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsPageContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsPageContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsPageContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsPageContent[]    findAll()
 * @method CmsPageContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsPageContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsPageContent::class);
    }

    // /**
    //  * @return CmsPageContent[] Returns an array of CmsPageContent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CmsPageContent
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
