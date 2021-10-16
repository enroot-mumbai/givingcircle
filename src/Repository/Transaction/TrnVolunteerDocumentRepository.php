<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnVolunteerDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnVolunteerDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnVolunteerDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnVolunteerDocument[]    findAll()
 * @method TrnVolunteerDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnVolunteerDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnVolunteerDocument::class);
    }

    // /**
    //  * @return TrnVolunteerDocument[] Returns an array of TrnVolunteerDocument objects
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
    public function findOneBySomeField($value): ?TrnVolunteerDocument
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
