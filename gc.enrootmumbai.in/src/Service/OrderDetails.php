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
}