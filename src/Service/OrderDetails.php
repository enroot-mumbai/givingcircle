<?php

namespace App\Service;

use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnOrder;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class OrderDetails
{
    /*
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Security
     */
    private $security;
    /**
     * @var CommonHelper
     */
    private $commonHelper;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * OrderDetails constructor.
     * @param EntityManagerInterface $em
     * @param CommonHelper $commonHelper
     * @param Security $security
     * @param SessionInterface $session
     */
    public function __construct(EntityManagerInterface $em, CommonHelper $commonHelper,Security $security, SessionInterface $session) {
        $this->em = $em;
        $this->commonHelper = $commonHelper;
        $this->security = $security;
        $this->session = $session;
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @return
     */
    public function getTopDonors(TrnCircleEvents $trnCircleEvents) {
        $repository = $this->em->getRepository(TrnOrder::class);
        return $repository->createQueryBuilder('o')
            ->select('o')
            ->leftJoin('o.trnCircleEvent', 'e')
//            ->andWhere('o.trnCircleEvent = :event_id')
            ->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id ')
            ->andWhere('o.transactionStatus= :transactionStatus')
            ->orderBy('o.transactionAmount', 'DESC')
            ->setParameter('event_id', $trnCircleEvents->getId())
            ->setParameter('transactionStatus', 'success')
            ->setMaxResults(5)
            ->getQuery()->getResult();
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @return mixed
     */
    public function getLatestDonor(TrnCircleEvents $trnCircleEvents) {
        $repository = $this->em->getRepository(TrnOrder::class);
        return $repository->createQueryBuilder('o')
            ->select('o')
            ->leftJoin('o.trnCircleEvent', 'e')
//            ->andWhere('o.trnCircleEvent = :event_id')
            ->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id ')
            ->andWhere('o.transactionStatus= :transactionStatus')
            ->orderBy('o.id', 'DESC')
            ->setParameter('event_id', $trnCircleEvents->getId())
            ->setParameter('transactionStatus', 'success')
            ->setMaxResults(5)
            ->getQuery()->getResult();
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @return mixed
     */
    public function getAllDonor(TrnCircleEvents $trnCircleEvents) {
        $repository = $this->em->getRepository(TrnOrder::class);
        return $repository->createQueryBuilder('o')
            ->select('o')
            ->leftJoin('o.trnCircleEvent', 'e')
//            ->andWhere('o.trnCircleEvent = :event_id')
            ->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id ')
            ->andWhere('o.transactionStatus= :transactionStatus')
            ->orderBy('o.id', 'DESC')
            ->setParameter('event_id', $trnCircleEvents->getId())
            ->setParameter('transactionStatus', 'success')
            ->getQuery()->getResult();
    }

    public function getDonationsByDetails($circle_id, $eventId, $volunteerId, $donorName, $appUser = null) {

        $repository = $this->em->getRepository(TrnOrder::class);
        $query = $repository->createQueryBuilder('o')
                    ->andWhere('o.isGivingCircleDonation = :gc_donation')
                    ->setParameter('gc_donation', 0);

        if(!empty($circle_id) || !empty($eventId) || !empty($volunteerId)) {
            $query = $query->leftJoin('o.trnCircleEvent', 'e');

            if($appUser != null) {
                $query = $query->andWhere('e.appUser = :user')
                    ->setParameter('user', $appUser);
            }

            if(!empty($circle_id)) {
                $query = $query->andWhere('e.trnCircle = :circle_id')
                    ->setParameter('circle_id', $circle_id);
            }

            if(!empty($eventId)) {
                $query = $query->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id ')
                    ->setParameter('event_id', $eventId);
            }

            if(!empty($volunteerId)) {
                $query = $query->andWhere('e.appUser = :user_id')
                    ->setParameter('user_id', $volunteerId);
            }
        } else if ($appUser != null) {
            $query = $query->leftJoin('o.trnCircleEvent', 'e');
            $query = $query->andWhere('e.appUser = :user')
                        ->setParameter('user', $appUser);
        }

        if(!empty($donorName)) {

            if($donorName == 'Anonymous') {

                $query = $query->andWhere('o.isAnonymousDonation = :anonymous')
                    ->setParameter('anonymous', 1);
            } else {

                $donorNameArr = explode($donorName, '');
                /*$query = $query->andWhere('o.userFirstName like :name_1 or o.userFirstName like :name_2')
                    ->andWhere('o.userLastName like :name_1 or o.userLastName like :name_2')
                    ->setParameter('name_1', $donorNameArr[0])
                    ->setParameter('name_2', $donorNameArr[1]);*/
                $query = $query->andWhere('o.userFirstName like :name')
                    ->setParameter('name', $donorName);
            }
        }

        return $query->getQuery()->getResult();
    }

}