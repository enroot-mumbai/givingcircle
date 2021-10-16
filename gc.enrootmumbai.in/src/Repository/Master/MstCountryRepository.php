<?php

namespace App\Repository\Master;

use App\Entity\Master\MstCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstCountry|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstCountry|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstCountry[]    findAll()
 * @method MstCountry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstCountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstCountry::class);
    }

    public function getCountryList($value)

    {
        $dql =  $this->createQueryBuilder('c')
            ->select('c.id, c.countryName as text')
            ->andWhere('c.countryName LIKE :val')
            ->setParameter('val', $value.'%')
            ->orderBy('c.countryName', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
        return $dql;

    }

    /**
     * @return array
     */
    public function getCountryCodes() {
        $dql =  $this->createQueryBuilder('c')
            ->select('c.phoneCode')
            ->orderBy('c.phoneCode', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        $arrReturnResult = array();
        foreach ($dql as $data) {
            if(empty($data['phoneCode']))
                continue;
            $arrReturnResult[$data['phoneCode']] = $data['phoneCode'];
        }
        return $arrReturnResult;
    }

    // /**
    //  * @return MasterCountries[] Returns an array of MasterCountries objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MasterCountries
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
