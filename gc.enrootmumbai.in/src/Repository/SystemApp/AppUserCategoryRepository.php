<?php

namespace App\Repository\SystemApp;

use App\Entity\SystemApp\AppUserCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AppUserCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppUserCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppUserCategory[]    findAll()
 * @method AppUserCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppUserCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUserCategory::class);
    }

    public function getUserCategory ()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :userCategory')
            ->setParameter('userCategory', 2)
            ->orderBy('t.id','DESC')
            ->getQuery()
            ->getResult();
    }

}
