<?php

namespace App\Repository\SystemApp;

use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method AppUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppUser[]    findAll()
 * @method AppUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUser::class);
    }

    public function getUserRol($value)
    {
        $dql =  $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
        return $dql;
    }

    public function getUserTypes()
    {
        $type = 1;
        $dql =  $this->createQueryBuilder('c')
            ->andWhere('c.appUserCategory = :val')
            ->setParameter('val', $type)
            ->getQuery()
            ->getArrayResult();

        foreach ($dql as $value) {

            $usertypes[$value['id']] = $value['userName'];
        }
        return $usertypes;
    }

    public function checkValidationKey($validationKey, $expiryDatetime) {

        $dql =  $this->createQueryBuilder('c')
            ->andWhere('c.userResetPasswordToken = :val')
            ->setParameter('val', $validationKey)
            ->andWhere('c.userResetPasswordTokenExpiry > :val2')
            ->setParameter('val2', $expiryDatetime)
            ->getQuery()
            ->getResult()
        ;
        return $dql;
    }

    public function getUserByCompanyId($company_id) {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.appUserInfo', 'i')
            ->andWhere('i.orgCompany = :company')
            ->setParameter('company', $company_id)
            ->getQuery()
            ->getResult();
    }


    public function getUserInfo ($user_id)
    {
        $dql = $this->createQueryBuilder('u')
            ->select('u.userName', 'u.userFirstName', 'u.userLastName')
            ->where('u.id= :val')
            ->setParameter('val', $user_id)
            ->getQuery()
            ->getResult()
        ;
        return $dql;
    }

    public function getUserByEmailId($strEmailId = '' ) {
        if(empty($strEmailId))
            return null;
        return $this->createQueryBuilder('a')
            ->leftJoin('a.appUserInfo', 'i')
            ->andWhere('i.userEmail = :userEmail or a.userName = :userEmail ')
            ->setParameter('userEmail', $strEmailId)
            ->getQuery()
            ->getResult();
    }


    public function getApplicationUser() {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.appUserCategory', 'i')
            ->andWhere('i.userCategory = :userCategory and a.isActive = :isActive ')
            ->setParameter('userCategory', 'Application')
            ->setParameter('isActive', 1)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function getBackEndUsers() {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.appUserCategory', 'i')
            ->andWhere('i.userCategory = :userCategory and a.isActive = :isActive ')
            ->setParameter('userCategory', 'System')
            ->setParameter('isActive', 1)
            ->getQuery()
            ->getResult();
    }

}
