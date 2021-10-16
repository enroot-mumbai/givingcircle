<?php

namespace App\Repository\Organization;

use App\Entity\Organization\OrgCompanyOffice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrgCompanyOffice|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrgCompanyOffice|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrgCompanyOffice[]    findAll()
 * @method OrgCompanyOffice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrgCompanyOfficeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrgCompanyOffice::class);
    }

    public function getOfficeByCompanyId($value)
    {
        $dql =  $this->createQueryBuilder('o')
            ->andWhere('o.orgcompany = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $dql;
    }

}
