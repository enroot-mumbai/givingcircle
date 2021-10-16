<?php

namespace App\Repository\Form;

use App\Entity\Form\FormLead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormLead|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormLead|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormLead[]    findAll()
 * @method FormLead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormLeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormLead::class);
    }

    // /**
    //  * @return CrmLead[] Returns an array of CrmLead objects
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
    public function findOneBySomeField($value): ?CrmLead
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
