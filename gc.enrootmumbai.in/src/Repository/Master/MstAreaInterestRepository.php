<?php

namespace App\Repository\Master;

use App\Entity\Master\MstAreaInterest;
use App\Service\ChangeMakerFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstAreaInterest|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstAreaInterest|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstAreaInterest[]    findAll()
 * @method MstAreaInterest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstAreaInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstAreaInterest::class);
    }

    public function getSecondaryAreaOfInterest($priamryAI) {

        return $this->createQueryBuilder('i')
            ->andWhere('i.isActive =:active')
            ->andWhere('i.mstAreaInterestPrimary = :primaryAI')
            ->setParameter('active',1)
            ->setParameter('primaryAI',$priamryAI)
            ->orderBy('i.sequenceNo', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAreaInterest()
    {
        $dql = $this->createQueryBuilder('i')
            ->andWhere('i.isActive =:active')
            ->setParameter('active',1)
            ->getQuery()
            ->getArrayResult();

        foreach ($dql as $value) {
            $interest[$value['areaInterest']] = $value['areaInterest'];
        }
        $interest['Other'] = "My cause is not covered";

        return $interest;
    }

    public function findOneBySeqNo()
    {
        return $this->createQueryBuilder('i')
            ->select('MAX(i.sequenceNo)')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
