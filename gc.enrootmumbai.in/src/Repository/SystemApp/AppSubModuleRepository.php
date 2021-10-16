<?php

namespace App\Repository\SystemApp;

use App\Entity\SystemApp\AppSubModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AppSubModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppSubModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppSubModule[]    findAll()
 * @method AppSubModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppSubModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppSubModule::class);
    }

    public function getSubModule($value)
    {
        $dql =  $this->createQueryBuilder('c')
            ->addSelect('m')
            ->select('c.id','c.subModuleStatic','c.subModuleName', 'c.subModuleValue', 'c.subModuleParentValue', 'm.moduleName','m.moduleValue', 'c.isChildMenu', 'r.roleName')
            ->leftJoin('c.appmodule', 'm')
            ->innerJoin('c.appRole', 'r')
            ->andWhere('m.id = :value')
            ->andWhere('c.isActive = 1')
            ->andWhere('c.subModuleDisplayMenu = 1')
            ->andWhere('c.parentId = 0')
            ->setParameter('value', $value)
            ->orderBy('c.sequenceNo', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $dql;
    }

    public function getSubModuleByModuleId($value)
    {
       return  $dql =  $this->createQueryBuilder('c')
            ->andWhere('c.appmodule = :val')
            ->setParameter('val', $value)
            ->orderBy('c.parentId', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSubModuleListByModuleId($value)
    {
        $dql =  $this->createQueryBuilder('c')
            ->andWhere('c.appmodule = :val')
            ->setParameter('val', $value)
            ->orderBy('c.sequenceNo', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
        $sm['None'] = 0;
        foreach ($dql as $value) {
        $sm[$value['subModuleName']] = $value['id'];
        }
        return $sm;
    }

    public function getChildModule($parentId, $isChild)
    {
       return  $this->createQueryBuilder('c')
           ->select('c.id','c.subModuleStatic','c.subModuleName', 'c.subModuleValue', 'c.parentId', 'c.subModuleParentValue', 'c.isChildMenu', 'c.subModuleDisplayMenu', 'r.roleName')
            ->innerJoin('c.appRole', 'r')
            ->andWhere('c.parentId = :val')
            ->andWhere('c.isChildMenu = :val2')
            ->andWhere('c.isActive = 1')
            ->setParameter('val', $parentId)
            ->setParameter('val2', $isChild)
            ->orderBy('c.sequenceNo', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findOneBySeqNo($value)
    {
            return $this->createQueryBuilder('c')
                ->select('MAX(c.sequenceNo)')
                ->andWhere('c.appmodule = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
    }


}
