<?php

namespace App\Controller\Reports;

use App\Entity\Media\MdaMedia;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Cms\CmsPage;
use App\Form\Cms\CmsPageType;
use App\Entity\Organization\OrgCompany;
use App\Repository\Cms\CmsPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;

/**
 * @Route("/core/reports/crowdfunding_calculator", name="report_crowdfunding_calculator_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CrowdfundingCalculatorController extends AbstractController
{
    private $commonHelper;
    /**
     * @var TrnCircleEventsRepository
     */
    private TrnCircleEventsRepository $trnCircleEventsRepository;

    public function __construct(CommonHelper $commonHelper, TrnCircleEventsRepository $trnCircleEventsRepository)
    {
        $this->commonHelper = $commonHelper;
        $this->trnCircleEventsRepository = $trnCircleEventsRepository;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $allActiveEvents = $this->trnCircleEventsRepository->findBy(['isActive' => 1, 'isCrowdFunding' => 1]);

        return $this->render('reports/crowdfunding_calculator/index.html.twig', [
            'label_title' => 'Crowdfunding Calculator',
            'allActiveEvents' => $allActiveEvents,
        ]);
    }

    /**
     * @Route("/", name="result", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function result(Request $request): Response
    {
        $eventName = $request->get('event');

        $details = $this->trnCircleEventsRepository->getEventsByName($eventName, true);

        return $this->render('reports/crowdfunding_calculator/result.html.twig', [
            'label_title' => 'Crowdfunding Calculator',
            'details' => $details,
        ]);
    }
}
