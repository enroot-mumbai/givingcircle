<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCrowdFundEventOfflineTransfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCrowdFundEventOfflineTransfer|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCrowdFundEventOfflineTransfer|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCrowdFundEventOfflineTransfer[]    findAll()
 * @method TrnCrowdFundEventOfflineTransfer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCrowdFundEventOfflineTransferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCrowdFundEventOfflineTransfer::class);
    }

    public function getEventPayments($eventId, $statusId = null)
    {
        $query = $this->createQueryBuilder('ot')
            ->leftJoin('ot.trnCircleEvent', 'e')
            ->andWhere('e.id =:event_id OR e.parentTrnCircleEvents =:event_id')
            ->setParameter('event_id', $eventId);

        if($statusId != null) {
            $query->andWhere('ot.mstStatus = :status_id')
                ->setParameter('status_id', $statusId);
        }

        $sql = $query->orderBy('ot.createdOn', 'DESC')->getQuery();

        /*echo '<pre>';
        print_r($sql->getParameters());
        dd($sql->getSQL());*/

        return $sql->getResult();
    }

    // /**
    //  * @return TrnCrowdFundEventOfflineTransfer[] Returns an array of TrnCrowdFundEventOfflineTransfer objects
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
    public function findOneBySomeField($value): ?TrnCrowdFundEventOfflineTransfer
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
