<?php

namespace App\Repository\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnMaterialReceivedAtCollectionCentre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnMaterialReceivedAtCollectionCentre|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnMaterialReceivedAtCollectionCentre|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnMaterialReceivedAtCollectionCentre[]    findAll()
 * @method TrnMaterialReceivedAtCollectionCentre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnMaterialReceivedAtCollectionCentreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnMaterialReceivedAtCollectionCentre::class);
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
        $query = $this->createQueryBuilder('m')
            ->where('m.trnCircleEvents in (:trnCircleEvent) ')
            ->andWhere('m.appUser = :appUser')
            ->andWhere('m.isActive = 1 ')
            ->setParameter('trnCircleEvent', $arrAllCircleEvent)
            ->setParameter('appUser', $appUser);
        if (!empty($arrParameters['from']) && !empty($arrParameters['to'])) {
            $query->andWhere('m.createdOn between :from and :to')
                ->setParameter('from', $arrParameters['from'])
                ->setParameter('to', $arrParameters['to']);
        }
        if (!empty($arrParameters['quicksearch'])) {
            $query->innerJoin('m.appUser', 'a')
                ->innerJoin('a.appUserInfo', 'i')
                ->innerJoin('m.trnCollectionCentreDetails', 'c')
                ->innerJoin('c.mstCity', 'mc')
                ->innerJoin('c.mstState', 'ms')
                ->innerJoin('c.mstCountry', 'mco')
                ->andWhere('i.userFirstName like :quicksearch or i.userLastName like :quicksearch or i.userEmail like :quicksearch 
                or c.address1 like :quicksearch or c.address2 like :quicksearch or mc.city like :quicksearch or ms.state like :quicksearch 
                or mco.country like :quicksearch ')
                ->setParameter('quicksearch', '%'.$arrParameters['quicksearch'].'%');
        }
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return TrnMaterialReceivedAtCollectionCentre[] Returns an array of TrnMaterialReceivedAtCollectionCentre objects
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
    public function findOneBySomeField($value): ?TrnMaterialReceivedAtCollectionCentre
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
