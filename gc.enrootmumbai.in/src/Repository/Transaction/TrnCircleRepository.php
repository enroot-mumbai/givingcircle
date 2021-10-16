<?php

namespace App\Repository\Transaction;

use App\Entity\Master\MstStatus;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircle|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircle|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircle[]    findAll()
 * @method TrnCircle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleRepository extends ServiceEntityRepository
{
    /**
     * @var TrnCircleEventLeadsRepository 
     */
    private $trnCircleEventLeadsRepository;
    public function __construct(ManagerRegistry $registry, TrnCircleEventLeadsRepository $trnCircleEventLeadsRepository)
    {
        parent::__construct($registry, TrnCircle::class);
        $this->trnCircleEventLeadsRepository = $trnCircleEventLeadsRepository;
    }

    public function getAppUserCircles($appUserId)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.circle as name')
            ->andWhere('c.appUser =:appUserId')
            ->setParameter('appUserId', $appUserId)
            ->orderBy('c.circle', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function getAppUserActiveCircles($appUserId, $objMstStatus)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.appUser =:appUserId')
            ->andWhere('c.mstStatus =:mstStatus')
            ->setParameter('appUserId', $appUserId)
            ->setParameter('mstStatus', $objMstStatus)
            ->orderBy('c.circle', 'ASC')
            ->getQuery()
            ->getResult();

    }

    /**
     * @param $orgCompanyId
     * @return mixed
     */
    public function getAppUsersWithCircles($orgCompanyId)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.appUser', 'a')
            ->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company')
            ->setParameter('active', 1)
            ->setParameter('company', $orgCompanyId)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getLatestCircles($orgCompanyId, MstStatus $objMstStatus) {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company')
            ->andWhere('c.mstStatus = :status')
            ->setParameter('active', 1)
            ->setParameter('company', $orgCompanyId)
            ->setParameter('status', $objMstStatus->getId())
            ->orderBy('c.createdOn','DESC')
            ->setMaxResults( 6 )
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $orgCompanyId
     * @param MstStatus $objMstStatus
     * @param string $highLightId
     * @param string $interestId
     * @return mixed
     */
    public function getAllCirclesBasedOnFilter($orgCompanyId, MstStatus $objMstStatus,$highLightId = '', $interestId = '', $searchText = '') {
        $q = $this->createQueryBuilder('c');

        $q->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company')
            ->andWhere('c.mstStatus = :status')
            ->setParameter('active', 1)
            ->setParameter('company', $orgCompanyId)
            ->setParameter('status', $objMstStatus->getId())
            ->orderBy('c.createdOn','DESC');

        if (!empty($highLightId)) {
            $q->innerJoin('c.mstHighlights', 'h')
                ->andWhere('h.id in (:highlight) ')
                ->setParameter('highlight', $highLightId);
        }
        if (!empty($interestId)) {
            $q->innerJoin('c.trnAreaOfInterests', 't')
                ->innerJoin('t.areaInterestPrimary','i')
                ->andWhere('i.id in (:interest) ')
                ->setParameter('interest', $interestId);
        }

        if (!empty($searchText)) {

            $q->innerJoin('c.mstCity', 'city')
                ->innerJoin('c.mstState', 'state')
                ->innerJoin('c.mstCountry', 'country')
                ->innerJoin('c.appUser', 'au')
                ->innerJoin('au.appUserInfo', 'aui')
                ->andWhere('c.circle like :srchText OR city.city like :srchText OR state.state like :srchText 
                                OR country.country like :srchText OR aui.userFirstName like :srchText OR aui.userLastName like :srchText')
                ->setParameter('srchText', '%'.$searchText.'%');
        }
        $query = $q->getQuery();
        return $query->execute();
    }

    // /**
    //  * @return TrnCircle[] Returns an array of TrnCircle objects
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

    public function getPrimaryAreaInterestList($company_id, $activeProjectsOnly = false)
    {
        $sql = $this->createQueryBuilder('a')
            ->select('tap.id', 'tap.areaInterest')
            ->leftJoin('a.trnAreaOfInterests', 'ta')
            ->leftJoin('ta.areaInterestPrimary', 'tap')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('tap.isActive =:active')
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->groupBy('tap.id')
            ->orderBy('tap.areaInterest', 'ASC');

        if($activeProjectsOnly == true) {
            $sql->leftJoin('a.mstStatus', 'mst')
                ->andWhere('mst.status =:masterStatus')
                ->setParameter('masterStatus', 'Activated');
        }

        $query = $sql->getQuery();

        /*echo '<pre>';
        print_r($query->getParameters());
        echo '</pre>';
        dd($query->getSQL());*/

        return $query->getResult();
    }

    public function getAppUserProjects(AppUser $appUser, MstStatus $objMstStatus) {

        $sql = $this->createQueryBuilder('c')
            ->where("c.isActive = :isActive")
            ->andWhere("c.appUser = :appUser")
            ->andWhere("c.mstStatus = :mstStatus")
            ->setParameter('isActive', 1)
            ->setParameter('appUser', $appUser)
            ->setParameter('mstStatus', $objMstStatus);
        $query = $sql->getQuery()->getResult();

        $myCoCorePrjs = $this->trnCircleEventLeadsRepository->createQueryBuilder('c')
            ->addSelect('tc')
            ->innerJoin('c.trnCircle','tc')
            ->where("c.isActive = :isActive")
            ->andWhere("tc.isActive = :isActive_id")
            ->andWhere("c.appUser = :appUser")
            ->andWhere("tc.mstStatus = :mstStatus")
            ->setParameter('isActive', 1)
            ->setParameter('appUser', $appUser)
            ->setParameter('mstStatus', $objMstStatus)
            ->setParameter('isActive_id', 1)
            ->getQuery()->getResult();
        foreach ($myCoCorePrjs as $prj) {
            $query[] = $prj->getTrnCircle();
        }
        return $query;
    }

    public function getCircleByName($orgCompanyId, MstStatus $objMstStatus, $searchText) {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company')
            ->andWhere('c.mstStatus = :status')
            ->andWhere('c.circle LIKE :name')
            ->setParameter('active', 1)
            ->setParameter('company', $orgCompanyId)
            ->setParameter('status', $objMstStatus->getId())
            ->setParameter('name', '%'.$searchText.'%')
            ->orderBy('c.createdOn','DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
