<?php

namespace App\Repository\Cms;

use App\Entity\Cms\CmsSocialPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsSocialPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsSocialPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsSocialPost[]    findAll()
 * @method CmsSocialPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsSocialPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsSocialPost::class);
    }

}
