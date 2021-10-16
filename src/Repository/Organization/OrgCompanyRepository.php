<?php

namespace App\Repository\Organization;

use App\Entity\Organization\OrgCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrgCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrgCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrgCompany[]    findAll()
 * @method OrgCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrgCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrgCompany::class);
    }

    public function getCompanyByOfficeType($value)
    {
        $dql =  $this->createQueryBuilder('c')
            ->andWhere('c.mstofficetype = :val')
            ->setParameter('val', $value)
            ->orderBy('c.companyName', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        $sm['None'] = 0;
        foreach ($dql as $value) {

            $sm[$value['companyName']] = $value['id'];
        }

        return $sm;
    }

}
