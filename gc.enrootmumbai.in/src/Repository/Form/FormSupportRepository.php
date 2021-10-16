<?php

namespace App\Repository\Form;

use App\Entity\Form\FormSupport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormSupport|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormSupport|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormSupport[]    findAll()
 * @method FormSupport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormSupportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormSupport::class);
    }

    // /**
    //  * @return CrmSupportForm[] Returns an array of CrmSupportForm objects
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
    public function findOneBySomeField($value): ?CrmSupportForm
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
