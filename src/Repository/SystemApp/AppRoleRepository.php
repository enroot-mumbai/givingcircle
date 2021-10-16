<?php

namespace App\Repository\SystemApp;

use App\Entity\SystemApp\AppRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AppRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppRole[]    findAll()
 * @method AppRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppRole::class);
    }

    public function getRoles()
    {
        $dql =  $this->createQueryBuilder('r')
            ->getQuery()
            ->getArrayResult()
        ;
        foreach ($dql as $value) {
            $roles[$value['roleDescription']] = $value['roleName'];
        }
        return $roles;
    }

    public function getLimitedRoles()
    {
        $dql =  $this->createQueryBuilder('r')
            ->andWhere('r.isOrgEnabled =:show')
            ->setParameter('show', 1)
            ->getQuery()
            ->getArrayResult()
        ;
        foreach ($dql as $value) {
            $roles[$value['roleDescription']] = $value['roleName'];
        }
        return $roles;
    }
}
