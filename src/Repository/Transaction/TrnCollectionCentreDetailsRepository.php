<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCollectionCentreDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCollectionCentreDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCollectionCentreDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCollectionCentreDetails[]    findAll()
 * @method TrnCollectionCentreDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCollectionCentreDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCollectionCentreDetails::class);
    }

    public function getCollectionCenterMaster($circleId, $userId) {

        $sql = $this->createQueryBuilder('cc')
            ->innerJoin('cc.mstStatus', 'mstatus')
            ->andWhere('cc.isActive = :active')
            ->andWhere('mstatus.status = :status')
            ->andWhere('cc.trnCircle = :circle OR cc.appUser = :user')
            ->setParameter('circle', $circleId)
            ->setParameter('user', $userId)
            ->setParameter('active', 1)
            ->setParameter('status', 'Activated')
            ->getQuery();

        return $sql->getResult();
    }

    // /**
    //  * @return TrnCollectionCentreDetails[] Returns an array of TrnCollectionCentreDetails objects
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
    public function findOneBySomeField($value): ?TrnCollectionCentreDetails
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
