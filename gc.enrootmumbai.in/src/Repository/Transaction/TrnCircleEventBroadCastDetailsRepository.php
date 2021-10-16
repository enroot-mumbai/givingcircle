<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventBroadCastDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventBroadCastDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventBroadCastDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventBroadCastDetails[]    findAll()
 * @method TrnCircleEventBroadCastDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventBroadCastDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventBroadCastDetails::class);
    }

    public function getEventBroadCastMessages($eventId, $orderBy) {

        $query = $this->createQueryBuilder('bm')
            ->leftJoin('bm.trnCircleEvent', 'e')
            ->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id')
            ->setParameter('event_id', $eventId);

        foreach ($orderBy as $orderFld => $orderVal) {

            if($orderFld == 'createdOn') {
                $query->orderBy('bm.createdOn', $orderVal);
            }
        }

        return $query->getQuery()->getResult();
    }

    public function getEventNParentBroadCastMessages($eventId, $parentId, $orderBy) {

        $query = $this->createQueryBuilder('bm')
            ->leftJoin('bm.trnCircleEvent', 'e');

        if($parentId != null) {
            $query->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id OR e.id = :parent_id');
            $query->setParameter('parent_id', $parentId);
        } else {
            $query->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id');
        }
        $query->setParameter('event_id', $eventId);

        foreach ($orderBy as $orderFld => $orderVal) {

            if($orderFld == 'createdOn') {
                $query->orderBy('bm.createdOn', $orderVal);
            }
        }

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return TrnCircleEventBroadCastDetails[] Returns an array of TrnCircleEventBroadCastDetails objects
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
    public function findOneBySomeField($value): ?TrnCircleEventBroadCastDetails
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
