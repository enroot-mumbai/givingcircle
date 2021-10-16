<?php

namespace App\Repository\SystemApp;

use App\Entity\SystemApp\AppUserCategory;
use App\Entity\SystemApp\AppUserInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AppUserInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppUserInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppUserInfo[]    findAll()
 * @method AppUserInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppUserInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUserInfo::class);
    }

    public function getFreeSubscriptionCheck($user_id)
    {
        $date = new \DateTime('now');

        return $this->createQueryBuilder('u')
            ->andWhere('u.appuser =:user')
            ->andWhere('u.freeProductCheckDate =:today')
            ->setParameter('user', $user_id)
            ->setParameter('today', $date->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function getOnlyApplicationUsers($objAppUserCategory){
        return $this->createQueryBuilder('u')
            ->leftJoin('u.appUser', 'a')
            ->where('a.appUserCategory =:appUserCategory')
            ->setParameter('appUserCategory', $objAppUserCategory)
            ->orderBy('a.id','desc')
            ->getQuery()
            ->getResult();
    }

    public function checkPanCardNoUnique($pancardNumber){
        return $this->createQueryBuilder('u')
            ->where('u.pancardNumber =:pancardNumber')
            ->setParameter('pancardNumber', $pancardNumber)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return AppUserInfo[] Returns an array of AppUserInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AppUserInfo
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
