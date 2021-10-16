<?php

namespace App\Repository\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnVolunteerCircleParticipationDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunteerCircleParticipationDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunteerCircleParticipationDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunteerCircleParticipationDetails[]    findAll()
 * @method TrnVolunteerCircleParticipationDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunteerCircleParticipationDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunteerCircleParticipationDetails::class);
    }

    /**
     * @param AppUser $appUser
     * @param array $arrAllCircleEvent
     * @param array $arrParameters
     * @return array
     */
    public function findParticipationDetails(AppUser $appUser, array $arrAllCircleEvent = array(), array
    $arrParameters = array()) :array
    {
        $query = $this->createQueryBuilder('v')
            ->where('v.trnCircleEvent in (:trnCircleEvent) ')
            ->andWhere('v.appUser = :appUser')
            ->andWhere('v.isActive = 1 ')
            ->setParameter('trnCircleEvent', $arrAllCircleEvent)
            ->setParameter('appUser', $appUser);
        if (!empty($arrParameters['from']) && !empty($arrParameters['to'])) {
            $query->andWhere('v.dateOfService between :from and :to')
                ->setParameter('from', $arrParameters['from'])
                ->setParameter('to', $arrParameters['to']);
        }
        if (!empty($arrParameters['quicksearch'])) {
            $query->innerJoin('v.appUser', 'a')
                ->innerJoin('a.appUserInfo', 'i')
                ->andWhere('i.userFirstName like :quicksearch or i.userLastName like :quicksearch or i.userEmail like :quicksearch ')
                ->setParameter('quicksearch', '%'.$arrParameters['quicksearch'].'%');
        }
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return TrnVolunteerCircleParticipationDetails[] Returns an array of TrnVolunteerCircleParticipationDetails objects
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
    public function findOneBySomeField($value): ?TrnVolunteerCircleParticipationDetails
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
