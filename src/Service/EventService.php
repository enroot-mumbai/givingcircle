<?php

namespace App\Service;

use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCrowdFundEventOfflineTransfer;
use App\Entity\Transaction\TrnOrder;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class EventService
{
    /*
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function getAllEventPayments($objEvent) {

        $resultArr = array();
        $trnCircleEventPayments = $this->em->getRepository(TrnOrder::class)->getEventOrders($objEvent->getId());

        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" =>  'Pending Activation']);

        $trnCircleEventOfflinePayments = $this->em->getRepository(TrnCrowdFundEventOfflineTransfer::class)->getEventPayments(
            $objEvent->getId(), $objMstStatus->getId());

        foreach ($trnCircleEventPayments as $eventPayment) {

            $resultSet = array();

            $resultSet['paymentMode'] = $eventPayment->getPaymentMode();
            if($objEvent->getId()  == $eventPayment->getTrnCircleEvent()->getId()) {
                $resultSet['event'] = 'Self';
            } else {
                $resultSet['event'] = $eventPayment->getTrnCircleEvent();
            }
            $resultSet['transactionId'] = $eventPayment->getTransactionId();
            $resultSet['date'] = $eventPayment->getOrderDateTime();
            $resultSet['amount'] = $eventPayment->getTransactionAmount();
            $resultSet['status'] = $eventPayment->getTransactionStatus();
            $resultSet['name'] = $eventPayment->getUserFirstName().' '.$eventPayment->getUserLastName();
            $resultSet['email'] = $eventPayment->getUserEmail();
            $resultSet['mobileNo'] = '+91-'.$eventPayment->getUserMobileNo();
            $resultSet['isAnonymous'] = $eventPayment->getIsAnonymousDonation();

            $resultArr[] = $resultSet;
        }

        foreach ($trnCircleEventOfflinePayments as $eventPayment) {

            $resultSet = array();

            $resultSet['paymentMode'] = 'Offline';
            if($objEvent->getId()  == $eventPayment->getTrnCircleEvent()->getId()) {
                $resultSet['event'] = 'Self';
            } else {
                $resultSet['event'] = $eventPayment->getTrnCircleEvent();
            }
            $resultSet['transactionId'] = $eventPayment->getBankTransactionId();
            $resultSet['date'] = $eventPayment->getCreatedOn();
            $resultSet['amount'] = $eventPayment->getAmountDonated();

            if($eventPayment->getMstStatus() == 'Pending Activation') {
                $resultSet['status'] = 'Not Approved';
            } else if($eventPayment->getMstStatus() == 'Activated') {
                $resultSet['status'] = 'Approved';
            } else {
                $resultSet['status'] = $eventPayment->getMstStatus();
            }

            $resultSet['name'] = $eventPayment->getFullName();
            $resultSet['email'] = $eventPayment->getEmail();
            $resultSet['mobileNo'] = $eventPayment->getMobileCountryCode().'-'.$eventPayment->getMobileNumber();
            $resultSet['isAnonymous'] = $eventPayment->getIsAnonymousDonation();

            $resultArr[] = $resultSet;
        }

        usort($resultArr, function($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return $resultArr;

    }

    function getEventsByDetails($event_id, $circle_id, $from_date, $to_date, $appUser = null) {

        $repository = $this->em->getRepository(TrnCircleEvents::class);
        $query = $repository->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->setParameter('active', 1)
            ->andWhere('e.isCrowdFunding = :is_crowdfunding')
            ->setParameter('is_crowdfunding', 0);

        if($appUser != null) {
            $query = $query->andWhere('e.appUser = :user')
                ->setParameter('user', $appUser);
        }

        if(!empty($event_id)) {
            $query = $query->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id')
                ->setParameter('event_id', $event_id);
        }
        if(!empty($circle_id)) {
            $query = $query->andWhere('e.trnCircle = :circle_id')
                ->setParameter('circle_id', $circle_id);
        }
        if(!empty($from_date)) {
            /*$query = $query->andWhere('e.fromDate >= :from_date')
                ->setParameter('from_date', $from_date);*/
        }

        if(!empty($to_date)) {
            /*$query = $query->andWhere('e.toDate <= :to_date')
                ->setParameter('to_date', $to_date);*/
        }

        return $query->getQuery()->getResult();

    }
}