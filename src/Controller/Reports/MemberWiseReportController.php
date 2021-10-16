<?php

namespace App\Controller\Reports;

use App\Entity\Master\MstEventProductType;
use App\Entity\Media\MdaMedia;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use App\Service\ProjectService;
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
 * @Route("/core/reports/member_wise_report", name="report_member_wise_report_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class MemberWiseReportController extends AbstractController
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
        //$allActiveEvents = $this->trnCircleEventsRepository->findBy(['isActive' => 1, 'isCrowdFunding' => 1]);
        $allMembers = $this->trnCircleEventsRepository->getEventCreatorList();

        return $this->render('reports/member_wise_report/index.html.twig', [
            'label_title' => 'Member Wise Report',
            'allMembers' => $allMembers,
            'allActiveProjects' => $allActiveProjects,
        ]);
    }

    /**
     * @Route("/core/reports/member_list", name="member_list", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function memberList(Request $request): Response
    {
        $circleId = $request->query->get('q');
        $allMembers = $this->trnCircleEventsRepository->getEventCreatorList($circleId);
        return $this->json($allMembers);
    }

    /**
     * @Route("/", name="result", methods={"GET","POST"})
     * @param Request $request
     * @param ProjectService $projectService
     * @return Response
     */
    public function result(Request $request, ProjectService $projectService): Response
    {
        $projectId = $request->get('project');
        $appUserId = $request->get('member'); // users who have created events or crowdfunding

        if(!empty($projectId) && !empty($appUserId)) {
            $details = $this->trnCircleEventsRepository->findBy(['isActive' => 1, 'trnCircle' => $projectId, 'appUser' => $appUserId]);
        } else if(!empty($projectId)){
            $details = $this->trnCircleEventsRepository->findBy(['isActive' => 1, 'trnCircle' => $projectId]);
        } else if(!empty($appUserId)){
            $details = $this->trnCircleEventsRepository->findBy(['isActive' => 1, 'appUser' => $appUserId]);
        } else {
            $details = $this->trnCircleEventsRepository->findBy(['isActive' => 1]);
        }

        $resultArr = array();

        foreach ($details as $detail) {
            $tempArr = array();

            $circle_id = $detail->getTrnCircle()->getId();
            $user_id = $detail->getAppUser()->getId();

            $resultArrKey = $circle_id.'_'.$user_id;

            if(!array_key_exists($resultArrKey, $resultArr)) {

                $tempArr = $projectService->getEventCountByProductType($circle_id, $user_id);

                $tempArr['circleName'] = $detail->getTrnCircle()->getCircle();
                $tempArr['circleId'] = $circle_id;
                $tempArr['memberName'] = $detail->getAppUser()->getAppUserInfo()->getName();
                $tempArr['memberId'] = $user_id;

                $resultArr[$resultArrKey] = $tempArr;
            }
        }

        return $this->render('reports/member_wise_report/result.html.twig', [
            'label_title' => 'Member Wise Report',
            'details' => $resultArr,
        ]);
    }
}
