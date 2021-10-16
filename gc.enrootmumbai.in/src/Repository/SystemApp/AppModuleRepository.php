<?php

namespace App\Repository\SystemApp;

use App\Entity\SystemApp\AppModule;
use App\Entity\SystemApp\AppUserCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method AppModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppModule[]    findAll()
 * @method AppModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppModule::class);
    }

    public function getModuleByUserType ($value)
    {
        $dql = $this->createQueryBuilder('c')
            ->leftJoin('c.appUserCategory', 'u')
            ->andWhere('c.isActive = :val2')
            ->andWhere('u.id= :val')
            ->setParameter('val', $value)
            ->setParameter('val2', 1)
            ->getQuery()
            ->getResult()
        ;
        return $dql;
    }

    public function getAppModule ()
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.moduleName', 'c.moduleValue', 'r.roleName')
            ->innerJoin('c.appRole', 'r')
            ->andWhere('c.isActive = :active')
            ->setParameter('active', 1)
            ->getQuery()
            ->getResult()
        ;
    }

}
