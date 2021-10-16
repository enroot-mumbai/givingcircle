<?php

namespace App\Controller\Reports;

use App\Entity\Master\MstEventProductType;
use App\Entity\Media\MdaMedia;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use App\Service\MyAccountService;
use App\Service\ProjectService;
use App\Service\ReportService;
use Container6EFEppJ\getCache_SecurityExpressionLanguageService;
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
 * @Route("/core/reports/event_performance", name="report_event_performance_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class EventPerformanceController extends AbstractController
{
    private $commonHelper;

    private TrnCircleRepository $trnCircleRepository;

    private TrnCircleEventsRepository $trnCircleEventsRepository;

    public function __construct(CommonHelper $commonHelper, TrnCircleRepository $trnCircleRepository, TrnCircleEventsRepository $trnCircleEventsRepository)
    {
        $this->commonHelper = $commonHelper;
        $this->trnCircleRepository = $trnCircleRepository;
        $this->trnCircleEventsRepository = $trnCircleEventsRepository;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $allActiveProjects = $this->trnCircleRepository->findBy(['isActive' => 1]);
        $allActiveEvents = $this->trnCircleEventsRepository->findBy(['isActive' => 1]);

        return $this->render('reports/event_performance/index.html.twig', [
            'label_title' => 'Event Performance',
            'allActiveEvents' => $allActiveEvents,
            'allActiveProjects' => $allActiveProjects,
        ]);
    }

    /**
     * @Route("/core/reports/event_list", name="event_list", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function eventList(Request $request): Response
    {
        $circleId = $request->query->get('q');
        $allActiveEvents = $this->trnCircleEventsRepository->getCircleEventList($circleId, 1);
        return $this->json($allActiveEvents);
    }

    /**
     * @Route("/", name="result", methods={"GET","POST"})
     * @param Request $request
     * @param ReportService $reportService
     * @return Response
     */
    public function result(Request $request, ReportService $reportService): Response
    {
        //dd($request);
        $projectId = $request->get('project');
        $eventId = $request->get('event');

        if(!empty($projectId) && !empty($eventId)) {
            $details = $this->trnCircleEventsRepository->findBy(['isActive' => 1, 'trnCircle' => $projectId, 'id' => $eventId]);
        } else if(!empty($projectId)){
            $details = $this->trnCircleEventsRepository->findBy(['isActive' => 1, 'trnCircle' => $projectId]);
        } else if(!empty($eventId)){
            $details = $this->trnCircleEventsRepository->findBy(['isActive' => 1, 'id' => $eventId]);
        } else {
            $details = $this->trnCircleEventsRepository->findBy(['isActive' => 1]);
        }

        $resultArr = $reportService->prepareEventPerformanceResult($details);

//dd($resultArr);

        return $this->render('reports/event_performance/result.html.twig', [
            'label_title' => 'Event Performance',
            'details' => $resultArr,
        ]);
    }
}
