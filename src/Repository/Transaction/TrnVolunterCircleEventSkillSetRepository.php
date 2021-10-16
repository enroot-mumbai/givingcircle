<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunterCircleEventSkillSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunterCircleEventSkillSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunterCircleEventSkillSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunterCircleEventSkillSet[]    findAll()
 * @method TrnVolunterCircleEventSkillSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunterCircleEventSkillSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunterCircleEventSkillSet::class);
    }

    // /**
    //  * @return TrnVolunterCircleEventSkillSet[] Returns an array of TrnVolunterCircleEventSkillSet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrnVolunterCircleEventSkillSet
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
