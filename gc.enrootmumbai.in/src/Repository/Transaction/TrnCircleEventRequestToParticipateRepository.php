<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventRequestToParticipate;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Master\MstEventProductTypeRepository;
use App\Repository\Master\MstStatusRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventRequestToParticipate|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventRequestToParticipate|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventRequestToParticipate[]    findAll()
 * @method TrnCircleEventRequestToParticipate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventRequestToParticipateRepository extends ServiceEntityRepository
{

    /**
     * @var MstStatusRepository
     */
    private $mstStatusRepository;

    /**
     * @var MstEventProductTypeRepository
     */
    private $mstEventProductTypeRepository;

    public function __construct(ManagerRegistry $registry, MstStatusRepository $mstStatusRepository,
                                MstEventProductTypeRepository $mstEventProductTypeRepository)
    {
        parent::__construct($registry, TrnCircleEventRequestToParticipate::class);
        $this->mstStatusRepository = $mstStatusRepository;
        $this->mstEventProductTypeRepository = $mstEventProductTypeRepository;
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @param array $arrParameters
     * @return array
     */
    public function findByCondition(TrnCircleEvents $trnCircleEvents, $arrParameters = array()) :array {
        $query = $this->createQueryBuilder('e');
        $query->innerJoin('e.trnCircleEvent', 'tce')
            ->where(" e.trnCircleEvent = :trnCircleEvent ")
            ->setParameter('trnCircleEvent', $trnCircleEvents);
        if (!empty($arrParameters['mstProductType'])) {
            $mstEventProductType = $this->mstEventProductTypeRepository->find($arrParameters['mstProductType']);
            $arrMstEventProductType = array($mstEventProductType->getId());
            $query->innerJoin('tce.mstEventProductType', 'p')
                ->andWhere('p.id in (:mstEventProductType_id)')
                ->setParameter('mstEventProductType_id', $arrMstEventProductType);
        }
        if (!empty($arrParameters['mstStatus'])) {
            $objMstStatus = $this->mstStatusRepository->find($arrParameters['mstStatus']);
            $query->andWhere('e.mstStatus = :mstStatus ')
                ->setParameter('mstStatus', $objMstStatus);
        }
        if (!empty($arrParameters['quicksearch'])) {
            $query->innerJoin('e.appUser', 'a')
                ->innerJoin('a.appUserInfo', 'i')
                ->andWhere('i.userEmail like :quicksearch OR i.userFirstName like :quicksearch OR i.userLastName like :quicksearch OR 
                            i.userMobileNumber like :quicksearch ');
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param array $arrAllCircleEvent
     * @param array $arrParameters
     * @param array $arrMstStats
     * @return array
     */
    public function findProjectRequestToJoinMemberList(array $arrAllCircleEvent = array(), array $arrParameters = array(),
                                                       array $arrMstStats = array() ) :array
    {
        $query = $this->createQueryBuilder('e');
        $query->innerJoin('e.trnCircleEvent', 'tce')
            ->where(" e.trnCircleEvent in (:arrTrnCircleEvent) ")
            ->setParameter('arrTrnCircleEvent', $arrAllCircleEvent);
        if (!empty($arrParameters['Events'])) {
            $query->andWhere('e.trnCircleEvent = :trnCircleEvent ')
                ->setParameter('trnCircleEvent', $arrParameters['Events']);
        }
        if (!empty($arrParameters['mstProductType'])) {
            $mstEventProductType = $this->mstEventProductTypeRepository->find($arrParameters['mstProductType']);
            $arrMstEventProductType = array($mstEventProductType->getId());
            $query->innerJoin('tce.mstEventProductType', 'p')
                ->andWhere('p.id in (:mstEventProductType_id)')
                ->setParameter('mstEventProductType_id', $arrMstEventProductType);
        }
        if (!empty($arrParameters['mstStatus'])) {
            $objMstStatus = $this->mstStatusRepository->find($arrParameters['mstStatus']);
            $query->andWhere('e.mstStatus = :mstStatus ')
                ->setParameter('mstStatus', $objMstStatus);
        }
        if (!empty($arrParameters['quicksearch'])) {
            $query->innerJoin('e.appUser', 'a')
                ->innerJoin('a.appUserInfo', 'i')
                ->andWhere('i.userEmail like :quicksearch OR i.userFirstName like :quicksearch OR i.userLastName like :quicksearch OR 
                            i.userMobileNumber like :quicksearch ');
        }
        return $query->getQuery()->getResult();
    }


    // /**
    //  * @return TrnCircleEventRequestToParticipate[] Returns an array of TrnCircleEventRequestToParticipate objects
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
    public function findOneBySomeField($value): ?TrnCircleEventRequestToParticipate
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
