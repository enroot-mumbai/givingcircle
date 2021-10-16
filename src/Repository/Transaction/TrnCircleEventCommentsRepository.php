<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\TrnCircleEventComments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrnCircleEventComments|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEventComments|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEventComments[]    findAll()
 * @method TrnCircleEventComments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventCommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrnCircleEventComments::class);
    }

    public function updateStatus($comment_id, $status)
    {
        if ($status == 'approve') {
            $id = 1;
        }
        if ($status == 'unapprove') {
            $id = 0;
        }
        $update = $this->createQueryBuilder('c')
            ->update()
            ->set('c.isApproved', ':status')
            ->setParameter('status', $id)
            ->andWhere('c.id =:id')
            ->setParameter('id', $comment_id)
            ->getQuery()
            ->execute();

        return $update;
    }
}
