<?php

namespace App\Controller\Reports;

use App\Entity\Master\MstEventProductType;
use App\Entity\Media\MdaMedia;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Service\CommonHelper;
use App\Service\EventService;
use App\Service\FileUploaderHelper;
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
 * @Route("/core/reports/impact_report", name="report_impact_report_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class ImpactReportController extends AbstractController
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

        return $this->render('reports/impact_report/index.html.twig', [
            'label_title' => 'Impact Report',
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
     * @param EventService $eventService
     * @param ReportService $reportService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function result(Request $request, EventService $eventService,
                           ReportService $reportService,
                           TrnCircleEventsRepository $trnCircleEventsRepository): Response
    {
        $projectId = $request->get('project');
        $eventId = $request->get('event');
        $from_date = $request->get('from_date'); // mm-dd-yyyy
        $to_date = $request->get('to_date'); // mm-dd-yyyy

        $fromDateArr = array();
        $toDateArr = array();
        $finalFromDate = '';
        $finalToDate = '';

        if(!empty($from_date)) {
            $fromDateArr = explode('/',$from_date);
            $fromDate_timestamp = strtotime($fromDateArr[2]. '-' .$fromDateArr[0]. '-' .$fromDateArr[1]); // Y-m-d
            $finalFromDate = date('Y-m-d', $fromDate_timestamp);
        }

        if(!empty($to_date)) {
            $toDateArr = explode('/',$to_date);
            $toDate_timestamp = strtotime($toDateArr[2]. '-' .$toDateArr[0]. '-' .$toDateArr[1]); // Y-m-d
            $finalToDate = date('Y-m-d', $toDate_timestamp);
        }

        /*
        $trnCircleEvents

        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        ([$trnCircleEvents], $entityManager);
        */

        $details = $eventService->getEventsByDetails($eventId, $projectId, $finalFromDate, $finalToDate);

        $returnArr = $reportService->prepareImpactReportResult($details);
        $resultArr = $returnArr['resultArr'];
        $volunteerTotalHoursAchieved = $returnArr['volunteerHrsAchieved'];
//dd($resultArr);
        return $this->render('reports/impact_report/result.html.twig', [
            'label_title' => 'Impact Report',
            'details' => $resultArr,
            'volunteer_ttl_hours_achieved' => $volunteerTotalHoursAchieved,
        ]);
    }
}
