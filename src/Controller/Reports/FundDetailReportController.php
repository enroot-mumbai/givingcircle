<?php

namespace App\Controller\Reports;

use App\Entity\Master\MstEventProductType;
use App\Entity\Media\MdaMedia;
use App\Entity\Partner\PtrPartnerDocuments;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Repository\Transaction\TrnOrderRepository;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use App\Service\OrderDetails;
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
 * @Route("/core/reports/fund_detail", name="report_fund_detail_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class FundDetailReportController extends AbstractController
{
    private $commonHelper;

    private TrnCircleRepository $trnCircleRepository;

    private TrnCircleEventsRepository $trnCircleEventsRepository;

    private TrnOrderRepository $trnOrderRepository;

    private OrderDetails $orderDetailService;

    public function __construct(CommonHelper $commonHelper, TrnCircleRepository $trnCircleRepository,
                                TrnCircleEventsRepository $trnCircleEventsRepository, TrnOrderRepository $trnOrderRepository,
                                OrderDetails $orderDetailService)
    {
        $this->commonHelper = $commonHelper;
        $this->trnCircleRepository = $trnCircleRepository;
        $this->trnCircleEventsRepository = $trnCircleEventsRepository;
        $this->trnOrderRepository = $trnOrderRepository;
        $this->orderDetailService = $orderDetailService;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $allActiveProjects = $this->trnCircleRepository->findBy(['isActive' => 1]);
        $allActiveEvents = $this->trnCircleEventsRepository->findBy(['isActive' => 1]);
        $allDonorList = $this->trnOrderRepository->getDonors();
        $allDonorList[] = ['id' => 'anonymous', 'name' => 'Anonymous'];

        return $this->render('reports/fund_detail/index.html.twig', [
            'label_title' => 'Crowdfunding or Funds Detailed Report',
            'allActiveEvents' => $allActiveEvents,
            'allActiveProjects' => $allActiveProjects,
            'allDonorList' => $allDonorList,
        ]);
    }

    /**
     * @Route("/", name="result", methods={"GET","POST"})
     * @param Request $request
     * @param ReportService $reportService
     * @return Response
     */
    public function result(Request $request, ReportService $reportService): Response
    {
        $projectId = $request->get('project');
        $eventId = $request->get('event');
        $volunteerId = $request->get('volunteer');
        $donorName = $request->get('donor');

        $details = $this->orderDetailService->getDonationsByDetails($projectId, $eventId, $volunteerId, $donorName);

        $resultArr = array();

        $returnArr = $reportService->prepareFundDetailResult($details);
        $resultArr = $returnArr['resultDetail'];
        $totalDonationReceivedArr = $returnArr['donationDetail'];

//dd($resultArr);

        return $this->render('reports/fund_detail/result.html.twig', [
            'label_title' => 'Crowdfunding or Funds Detailed Report',
            'details' => $resultArr,
            'donationArr' => $totalDonationReceivedArr,
        ]);
    }
}
