<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsBanner;
use App\Entity\Cms\CmsPage;
use App\Form\Cms\CmsBannerType;
use App\Repository\Cms\CmsBannerRepository;
use App\Service\FileUploaderHelper;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsController extends AbstractController
{
    /**
     * @Route("/core/cms", name="cms")
     */

    public function index()
    {

        return $this->render('layout/index.html.twig');
    }
}
