<?php

namespace App\Repository\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnOrder;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnOrder[]    findAll()
 * @method TrnOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnOrder::class);
    }

    public function updateTransaction($log, $transaction_id, $transaction_status, $id, $userOrderNo, $userInvoiceNo)
    {
        $orderDateTime = new DateTime('now', new DateTimeZone('UTC'));

        return $this->createQueryBuilder('t')
            ->update()
            ->set('t.transactionLog', ':log')
            ->set('t.transactionId', ':transactionId')
            ->set('t.transactionStatus', ':transactionStatus')
            ->set('t.orderDateTime', ':orderDateTime')
            ->set('t.userOrderNo', ':userOrderNo')
            ->set('t.userInvoiceNo', ':userInvoiceNo')
            ->setParameter('log', $log)
            ->setParameter('transactionId', $transaction_id)
            ->setParameter('transactionStatus', $transaction_status)
            ->setParameter('orderDateTime', $orderDateTime->format('Y-m-d H:i:s'))
            ->setParameter('userOrderNo', $userOrderNo)
            ->setParameter('userInvoiceNo', $userInvoiceNo)

            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    public function updateVerifyPaymentLog($log, $status, $id)
    {
        return $this->createQueryBuilder('t')
            ->update()
            ->set('t.verifyPaymentLog', ':log')
            ->set('t.verifyPaymentStatus', ':status')
            ->setParameter('log', $log)
            ->setParameter('status', $status)

            ->andWhere('t.id =:id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    public function getOrderPromotionUsageByPromotionId($promotion_id)
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.mktPromotion) as promotion')
            ->andWhere('o.transactionStatus =:status')
            ->andWhere('o.promotionCode =:promotion')
            ->setParameter('status', 'success')
            ->setParameter('promotion', $promotion_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getOrderPromotionUsageByPromotionAndUserId($promotion_id, $user_id)
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.mktPromotion) as promotion')
            ->andWhere('o.appUser =:user')
            ->andWhere('o.transactionStatus =:status')
            ->andWhere('o.mktPromotion =:promotion')
            ->setParameter('user', $user_id)
            ->setParameter('status', 'success')
            ->setParameter('promotion', $promotion_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getSubscription($fromDate, $toDate)
    {
        $query = $this->createQueryBuilder('o')
            ->andWhere('o.orderDateTime between :fromdate AND :todate')
            ->setParameter('fromdate', $fromDate)
            ->setParameter('todate', $toDate)
            ->orderBy('o.orderDateTime', 'DESC');
        $result = $query->getQuery()->getResult();
        return $result;

    }

    public function getTotalSubscriptionByUserCategory($app_user_category_id)
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id) as order')
            ->leftJoin('o.appUserCategory', 'c')
            ->andWhere('c.id =:usercategory')
            ->andWhere('o.transactionStatus =:status')
            ->setParameter('usercategory', $app_user_category_id)
            ->setParameter('status', 'captured')
//            ->groupBy('o.ptrPartner')
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function getEventOrders($eventId)
    {
        $query = $this->createQueryBuilder('o')
            ->leftJoin('o.trnCircleEvent', 'e')
            ->andWhere('e.id =:event_id OR e.parentTrnCircleEvents =:event_id')
            ->setParameter('event_id', $eventId)
            ->orderBy('o.orderDateTime', 'DESC')
            ->getQuery();

        /*echo '<pre>';
        print_r($query->getParameters());
        dd($query->getSQL());*/

        return $query->getResult();
    }

    public function getDonors($appUser = null)
    {
        $query = $this->createQueryBuilder('o')
//            ->select('o.id',"CONCAT(CASE WHEN o.userFirstName IS NULL THEN '' ELSE o.userFirstName END,' ',
//                        CASE WHEN o.userLastName IS NULL THEN '' ELSE o.userLastName END) AS name")
            ->select('o.id',"o.userFirstName AS name")
            ->andWhere('o.transactionStatus = :tstatus')
            ->andWhere('o.isAnonymousDonation = :anonymous')
            ->setParameter('tstatus', 'success')
            ->setParameter('anonymous', 0);

        if($appUser != null) {
            $query = $query->leftJoin('o.trnCircleEvent', 'e')
                ->andWhere('e.appUser =:user')
                ->setParameter('user', $appUser);
        }

        $query = $query->orderBy('o.id', 'DESC')
            ->getQuery();

        /*echo '<pre>';
        print_r($query->getParameters());
        dd($query->getSQL());*/

        return $query->getResult();
    }

    /**
     * @param string $transactionStatus
     * @param TrnCircleEvents $trnCircleEvents
     * @param array $arrParameters
     * @return array
     */
    public function getOrderByDetails($transactionStatus = "success", TrnCircleEvents $trnCircleEvents,
                                      $arrParameters = array()) :array {
        $query = $this->createQueryBuilder('o')
                      ->where('o.transactionStatus = :transactionStatus')
                      ->andWhere('o.trnCircleEvent = :trnCircleEvents')
                      ->setParameter('transactionStatus', $transactionStatus)
                      ->setParameter('trnCircleEvents', $trnCircleEvents);
        if( !empty($arrParameters['quicksearch'])) {
            $query->andWhere('o.userFirstName like :quicksearch or o.userFirstName like :quicksearch or o.userEmail like :quicksearch ')
                ->setParameter('quicksearch', $arrParameters['quicksearch']);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param string $transactionStatus
     * @param AppUser $appUser
     * @param array $arrTrnCircleEvents
     * @param array $arrParameters
     * @return array
     */
    public function getOrderByDetailsMultiEvents($transactionStatus = "success", AppUser $appUser, array $arrTrnCircleEvents
    = array(), $arrParameters = array()) :array
    {
        $query = $this->createQueryBuilder('o')
            ->where('o.transactionStatus = :transactionStatus')
            ->andWhere('o.appUser =:user')
            ->andWhere('o.trnCircleEvent in (:trnCircleEvents)')
            ->setParameter('user', $appUser)
            ->setParameter('transactionStatus',  $transactionStatus)
            ->setParameter('trnCircleEvents', $arrTrnCircleEvents);
        if (!empty($arrParameters['quicksearch'])) {
            $query->andWhere('o.userFirstName like :quicksearch or o.userFirstName like :quicksearch or o.userEmail like :quicksearch ')
                ->setParameter('quicksearch', '%'.$arrParameters['quicksearch'].'%');
        }
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return TrnOrder[] Returns an array of TrnOrder objects
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
    public function findOneBySomeField($value): ?TrnOrder
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
