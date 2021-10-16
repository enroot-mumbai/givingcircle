<?php

namespace App\Repository\Master;

use App\Entity\Master\MstSocial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MstSocial|null find($id, $lockMode = null, $lockVersion = null)
 * @method MstSocial|null findOneBy(array $criteria, array $orderBy = null)
 * @method MstSocial[]    findAll()
 * @method MstSocial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MstSocialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MstSocial::class);
    }

}
