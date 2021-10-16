<?php

namespace App\Repository\Form;

use App\Entity\Form\FormReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormReport[]    findAll()
 * @method FormReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormReport::class);
    }

}
