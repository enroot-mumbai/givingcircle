<?php

namespace App\Repository\Master;

use App\Entity\Master\MstSkillSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstSkillSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstSkillSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstSkillSet[]    findAll()
 * @method MstSkillSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstSkillSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstSkillSet::class);
    }

    // /**
    //  * @return MstSkillSet[] Returns an array of MstSkillSet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MstSkillSet
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getOneDefault()
    {
        $sql = $this->createQueryBuilder('c')
            ->andWhere('c.isDefault = :val')
            ->setParameter('val', 1)
            ->getQuery();

        return $sql->getResult();
    }

}
