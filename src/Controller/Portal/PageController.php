<?php

namespace App\Controller\Portal;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsArticleComment;
use App\Entity\Cms\CmsFaq;
use App\Entity\Cms\CmsPage;
use App\Entity\Cms\CmsPressRoom;
use App\Entity\Cms\CmsUserSubscription;
use App\Entity\Cms\CmsUserTestimonial;
use App\Entity\Form\FormReport;
use App\Entity\Form\FormSupport;
use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstHighlights;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEventRequestToParticipate;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Organization\OrgCompanyOffice;
use App\Entity\Transaction\TrnCircleRequestToJoin;
use App\Entity\Transaction\TrnOrder;
use App\Form\Cms\CmsUserCommentType;
use App\Form\Form\FormReportOtherType;
use App\Form\Form\FormReportSelfType;
use App\Form\Form\FormSupportChangeMakerType;
use App\Form\Form\FormSupportVolunteerType;
use App\Form\Transaction\TrnCircleEventCommentsType;
use App\Form\Transaction\TrnUserCommentsType;
use App\Repository\Cms\CmsUserSubscriptionRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Service\ChangeMakerFilter;
use App\Service\FileUploaderHelper;
use App\Service\MyAccountService;
use App\Service\NewsFilter;
use App\Service\NotificationService;
use App\Service\ProjectService;
use ContainerTQ3rU5N\getCmsPageContentTypeService;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Service\SearchHelper;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET", "POST"})
     * @param TrnCircleRepository $trnCircleRepository
     * @param SessionInterface $session
     * @return Response
     */
    public function home(TrnCircleRepository $trnCircleRepository, SessionInterface $session): Response
    {
        $areaInterests = $this->getDoctrine()->getRepository(CmsArticle::class)->getAreaInterestList(2,
            $this->getParameter('company_id'));

        // Volunteer Diaries Details
        $volunteer_articles = $this->getDoctrine()->getRepository(CmsArticle::class)->findBy(['mstArticleCategory' => 3,
            'orgCompany' => $this->getParameter('company_id'), 'isActive' => 1], ['sequenceNo' => 'ASC'], 4, 0);
        $total_volunteers = $this->getDoctrine()->getRepository(CmsArticle::class)->getArticleCount(3, 1);
        $cmsPage = $this->getDoctrine()->getRepository(CmsPage::class)->getContentBySlugName('volunteer-diaries');

        // Testimonials Details
        $testimonialPage = $this->getDoctrine()->getRepository(CmsPage::class)->getContentBySlugName('testimonials');
        $testimonialsList = $this->getDoctrine()->getRepository(CmsUserTestimonial::class)->findBy(['orgCompany' => $this->getParameter('company_id'),
            'isActive' => 1], ['createDateTime' => 'DESC'], 3, 0);

        // News Details
        $newsList = $this->getDoctrine()->getRepository(CmsPressRoom::class)->findBy(['orgCompany' => $this->getParameter('company_id'),
            'isActive' => 1], ['articleDateTime' => 'DESC'], 3, 0);

        $counter = 0;
        $newsArray = array();
        $latestNews = array();
        foreach ($newsList as $items) {
            if($counter == 0) {
                $latestNews = $items;
                $counter++;
            } else {
                $newsArray[] = $items;
            }
        }
        $session->remove('redirectURLName');
        $session->remove('redirectSubEventId');
        $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $arrCircleList =  $trnCircleRepository->getLatestCircles($this->getParameter('company_id'), $objMstStatus);
        $currentDateTime = new DateTime();
        return $this->render('portal/home/index.html.twig', [
            'areaInterests' => $areaInterests,
            'arrCircleList' => $arrCircleList,
            'currentDateTime' => $currentDateTime,
            'volunteer_articles' => $volunteer_articles,
            'total_volunteers' => $total_volunteers,
            'cmsPage' => $cmsPage,
            'latestNews' => $latestNews,
            'newsList' => $newsArray,
            'testimonialPage' => $testimonialPage,
            'testimonialsList' => $testimonialsList
        ]);
    }

    /**
     * @Route("/about-us", name="about-us", methods={"GET", "POST"})
     */
    public function aboutUs(): Response
    {
        //$pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'about-us', 'orgCompany' => $this->getParameter('company_id')]);

        $teamContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'the-team', 'slugName' => 'the-team', 'isActive' => 1, 'orgCompany' => $this->getParameter('company_id')]);
        $innerCircleContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'our-inner-circle', 'slugName' => 'our-inner-circle', 'isActive' => 1, 'orgCompany' => $this->getParameter('company_id')]);

        $teamList = $this->getDoctrine()->getRepository(CmsPage::class)->getListByRouteSlug('the-team', 'the-team', $this->getParameter('company_id'));

        $orderByArr = array(
            ['field'=> 'displaySequence', 'order' => 'ASC']
        );

        $innerCircleList = $this->getDoctrine()->getRepository(CmsPage::class)->getListByRouteSlug('our-inner-circle', 'our-inner-circle',$this->getParameter('company_id'), $orderByArr);

        return $this->render('portal/page/aboutus.html.twig', [
            //'page_content' => $pageContent,
            'teamContent' => $teamContent,
            'teamList' => $teamList,
            'innerCircleContent' => $innerCircleContent,
            'innerCircleList' => $innerCircleList,
        ]);
    }

    /**
     * @Route("/how-it-works", name="how-it-works", methods={"GET", "POST"})
     */
    public function howItWorks(): Response
    {
        return $this->render('portal/page/howitworks.html.twig', [
        ]);
    }

    /**
     * @Route("/plans-n-price", name="plans-n-price", methods={"GET", "POST"})
     */
    public function plansNPrice(): Response
    {
        return $this->render('portal/page/plansnprice.html.twig', [
        ]);
    }

    /**
     * @Route("/ngo", name="ngo", methods={"GET", "POST"})
     */
    public function ngo(): Response
    {
        return $this->render('portal/page/ngo.html.twig', [
        ]);
    }

    /**
     * @Route("/corporates", name="corporates", methods={"GET", "POST"})
     */
    public function corporates(): Response
    {
        return $this->render('portal/page/corporate.html.twig', [
        ]);
    }

    /**
     * @Route("/academia", name="academia", methods={"GET", "POST"})
     */
    public function academia(): Response
    {
        return $this->render('portal/page/academia.html.twig', [
        ]);
    }

    /**
     * @Route("/crowdfunding-campaigns", name="crowdfunding-campaigns", methods={"GET", "POST"})
     */
    public function crowdfundingCampaigns(): Response
    {
        return $this->render('portal/page/crowdfundingcampaigns.html.twig', [
        ]);
    }

    /**
     * @Route("/report-abuse", name="report-abuse", methods={"GET", "POST"})
     */
    public function reportAbuse(): Response
    {
        return $this->render('portal/page/reportabuse.html.twig', [
        ]);
    }

    /**
     * @Route("/the-concept", name="the-concept", methods={"GET", "POST"})
     */
    public function theConcept(): Response
    {
        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'the-concept', 'orgCompany' => $this->getParameter('company_id')]);
        return $this->render('portal/page/theconcept.html.twig', [
            'page_content' => $pageContent
        ]);
    }

    /**
     * @Route("/our-offering", name="our-offering", methods={"GET", "POST"})
     */
    public function offerings(): Response
    {
        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'our-offering', 'orgCompany' => $this->getParameter('company_id')]);
        return $this->render('portal/page/offering.html.twig', [
            'page_content' => $pageContent
        ]);
    }

    /**
     * @Route("/contact-us", name="contact-us", methods={"GET", "POST"})
     */
    public function contact(): Response
    {
        $officeList = $this->getDoctrine()->getRepository(OrgCompanyOffice::class)->findBy(['orgCompany' => $this->getParameter('company_id'), 'isActive' => 1]);

//        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'contact-us', 'orgCompany' => $this->getParameter('company_id')]);

//        $weAreNewContent = $this->getDoctrine()->getRepository(CmsPage::class)->getContentBySlugName('we-are-new');

        return $this->render('portal/page/contactus.html.twig', [
//            'page_content' => $pageContent,
            'office_list' => $officeList,
//            'we_are_new' => $weAreNewContent
        ]);
    }

    /**
     * @Route("/report-goodness", name="report-goodness", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function reportGoodness(): Response
    {
        return $this->render('portal/page/reportgoodness.html.twig', [
        ]);
    }

    /**
     * @Route("/report-goodness-self", name="report-goodness-self", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function reportGoodnessSelf(Request $request): Response
    {
        $formReport = new FormReport();
        $formReportSelf = $this->createForm(FormReportSelfType::class, $formReport);
        $formReportSelf->handleRequest($request);

        if ($formReportSelf->isSubmitted() && $formReportSelf->isValid()) {

            /*
            if (!empty($_POST['share-collateral']) && $_POST['share-collateral'] == 'on')
            {
                if (!empty($_FILES['uploadFile'])) {
                    $timestamp = date("ymdhis");
                    $newFileName = $timestamp . '_' . $_FILES['uploadFile']['name'];
                    $fileUploadName = str_replace(' ', '_', $newFileName);
                    move_uploaded_file($_FILES['uploadFile']['tmp_name'], "uploads/files/" . $fileUploadName);
                    $formReport->setUploadFile($fileUploadName);
                    $formReport->setUploadFilePath($this->getParameter('upload_file'));
                }
            }
            */
            /*print_r($_POST);
            dd($formReport);*/
            if (!empty($_POST['digital-presence']) && $_POST['digital-presence'] == 'yes')
            {
                $formReport->setDigitalPresence($_POST['digitalPresence']);
            }
            $formReport->setCityName($formReport->getMstCity()->getCity());
            $formReport->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $formReport->setCreateDateTime(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formReport);
            $entityManager->flush();
            return $this->redirectToRoute('report-goodness-thank-you');
        }


        return $this->render('portal/page/reportgoodnessself.html.twig', [
            'form_report_self' => $formReportSelf->createView(),
        ]);
    }

    /**
     * @Route("/report-goodness-else", name="report-goodness-else", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function reportGoodnessElse(Request $request): Response
    {
        $formReport = new FormReport();
        $formReportOther = $this->createForm(FormReportOtherType::class, $formReport);
        $formReportOther->handleRequest($request);

        if ($formReportOther->isSubmitted() && $formReportOther->isValid()) {

            /*
            if (!empty($_POST['share-collateral2']) && $_POST['share-collateral2'] == 'on')
            {
                if (!empty($_FILES['uploadFile2'])) {
                    $timestamp = date("ymdhis");
                    $newFileName = $timestamp . '_' . $_FILES['uploadFile2']['name'];
                    $fileUploadName = str_replace(' ', '_', $newFileName);
                    move_uploaded_file($_FILES['uploadFile2']['tmp_name'], "uploads/files/" . $fileUploadName);
                    $formReport->setUploadFile($fileUploadName);
                    $formReport->setUploadFilePath($this->getParameter('upload_file'));
                }
            }*/

            /*print_r($_POST);
            dd($formReport);*/
            if (!empty($_POST['digital-presence']) && $_POST['digital-presence'] == 'yes') {
                $formReport->setDigitalPresence($_POST['digitalPresence']);
            }
            $formReport->setCityName($formReport->getMstCity()->getCity());
            $formReport->setOtherCityName($formReport->getMstCityOther()->getCity());
            $formReport->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $formReport->setCreateDateTime(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formReport);
            $entityManager->flush();
            return $this->redirectToRoute('report-goodness-thank-you');
        }

        return $this->render('portal/page/reportgoodnesselse.html.twig', [
            'form_report_other' => $formReportOther->createView(),
        ]);
    }

    /**
     * @Route("/report-goodness-thank-you", name="report-goodness-thank-you", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function reportGoodnessThankYou(): Response
    {
        return $this->render('portal/page/reportgoodnessthankyou.html.twig', [
        ]);
    }


    /*
    public function reportGoodness(Request $request): Response
    {
        $formReport = new FormReport();
        $formReportSelf = $this->createForm(FormReportSelfType::class, $formReport);
        $formReportSelf->handleRequest($request);
        $formReportOther = $this->createForm(FormReportOtherType::class, $formReport);
        $formReportOther->handleRequest($request);

        if ($formReportSelf->isSubmitted() && $formReportSelf->isValid()) {

            if (!empty($_POST['share-collateral']) && $_POST['share-collateral'] == 'on')
            {
                if (!empty($_FILES['uploadFile'])) {
                    $timestamp = date("ymdhis");
                    $newFileName = $timestamp . '_' . $_FILES['uploadFile']['name'];
                    $fileUploadName = str_replace(' ', '_', $newFileName);
                    move_uploaded_file($_FILES['uploadFile']['tmp_name'], "uploads/files/" . $fileUploadName);
                    $formReport->setUploadFile($fileUploadName);
                    $formReport->setUploadFilePath($this->getParameter('upload_file'));
                }
            }
            if (!empty($_POST['digital-presence']) && $_POST['digital-presence'] == 'on')
            {
                $formReport->setDigitalPresence($_POST['digitalPresence']);
            }
            $formReport->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $formReport->setCreateDateTime(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formReport);
            $entityManager->flush();
            return $this->redirectToRoute('thank-you');
        }

        if ($formReportOther->isSubmitted() && $formReportOther->isValid()) {

            if (!empty($_POST['share-collateral2']) && $_POST['share-collateral2'] == 'on')
            {
                if (!empty($_FILES['uploadFile2'])) {
                    $timestamp = date("ymdhis");
                    $newFileName = $timestamp . '_' . $_FILES['uploadFile2']['name'];
                    $fileUploadName = str_replace(' ', '_', $newFileName);
                    move_uploaded_file($_FILES['uploadFile2']['tmp_name'], "uploads/files/" . $fileUploadName);
                    $formReport->setUploadFile($fileUploadName);
                    $formReport->setUploadFilePath($this->getParameter('upload_file'));
                }
            }
            if (!empty($_POST['digital-presence2']) && $_POST['digital-presence2'] == 'on') {
                $formReport->setDigitalPresence($_POST['digitalPresence2']);
            }
            $formReport->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $formReport->setCreateDateTime(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formReport);
            $entityManager->flush();
            return $this->redirectToRoute('thank-you');
        }

        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'report']);
        return $this->render('portal/page/reportgoodness.html.twig', [
            'page_content' => $pageContent,
            'form_report_self' => $formReportSelf->createView(),
            'form_report_other' => $formReportOther->createView(),
        ]);
    }*/

    /**
     * @Route("/partners", name="partners", methods={"GET", "POST"})
     */
    public function partners(): Response
    {
//        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'partners', 'orgCompany' => $this->getParameter('company_id')]);
        return $this->render('portal/page/partners.html.twig', [
//            'page_content' => $pageContent
        ]);
    }

    /**
     * @Route("/terms-and-conditions", name="terms-and-conditions", methods={"GET", "POST"})
     */
    public function termsAndCondition(): Response
    {
        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'terms-and-conditions', 'orgCompany' => $this->getParameter('company_id')]);
        return $this->render('portal/page/termsandconditions.html.twig', [
            'page_content' => $pageContent
        ]);
    }

    /**
     * @Route("/privacy-policy", name="privacy-policy", methods={"GET", "POST"})
     */
    public function privacyPolicy(): Response
    {
        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'privacy-policy', 'orgCompany' => $this->getParameter('company_id')]);
        return $this->render('portal/page/privacypolicy.html.twig', [
            'page_content' => $pageContent
        ]);
    }

    /**
     * @Route("/cookie-policy", name="cookie-policy", methods={"GET", "POST"})
     */
    public function cookiePolicy(): Response
    {
        return $this->render('portal/page/cookiepolicy.html.twig', [
        ]);
    }

    /**
     * @Route("/disclaimer", name="disclaimer", methods={"GET", "POST"})
     */
    public function disclaimer(): Response
    {
        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'disclaimer', 'orgCompany' => $this->getParameter('company_id')]);
        return $this->render('portal/page/disclaimer.html.twig', [
            'page_content' => $pageContent
        ]);
    }

    /**
     * @Route("/support", name="support", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function support(Request $request): Response
    {
        $formSupport = new FormSupport();
        $formSupportChangeMaker = $this->createForm(FormSupportChangeMakerType::class, $formSupport);
        $formSupportChangeMaker->handleRequest($request);
        $formSupportVolunteer = $this->createForm(FormSupportVolunteerType::class, $formSupport);
        $formSupportVolunteer->handleRequest($request);

        if ($formSupportChangeMaker->isSubmitted() && $formSupportChangeMaker->isValid()) {

            if ($formSupportChangeMaker->get('otherInterestType')->getData() != '')
            {
                $formSupport->setInterestType($formSupportChangeMaker->get('otherInterestType')->getData());
            }
            $formSupport->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $formSupport->setCreateDateTime(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formSupport);
            $entityManager->flush();
            return $this->redirectToRoute('thank-you');
        }

        if ($formSupportVolunteer->isSubmitted() && $formSupportVolunteer->isValid()) {

            if ($formSupportVolunteer->get('otherInterestType')->getData() != '')
            {
                $formSupport->setInterestType($formSupportVolunteer->get('otherInterestType')->getData());
            }
            $formSupport->setHelpType($_POST['helpType']);
            $formSupport->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $formSupport->setCreateDateTime(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formSupport);
            $entityManager->flush();
            return $this->redirectToRoute('thank-you');
        }

        return $this->render('portal/page/support.html.twig', [
            'form_support_change_maker' => $formSupportChangeMaker->createView(),
            'form_support_volunteer' => $formSupportVolunteer->createView(),
        ]);
    }

    /**
     * @Route("/change-makers", name="change-makers", methods={"GET", "POST"})
     */
    public function changeMakers(): Response
    {
        $areaInterests = $this->getDoctrine()->getRepository(CmsArticle::class)->getAreaInterestList(2,
            $this->getParameter('company_id'));
        /*return $this->render('portal/page/changemakers.html.twig', [
            'area_interests' => $areaInterests,
        ]);*/
        return $this->render('portal/change-maker/change-maker-index.html.twig', [
            'area_interests' => $areaInterests,
        ]);

    }

    /**
     * @Route("/change-makers/filter", name="change-makers-filter", methods={"GET", "POST"})
     * @param Request $request
     * @param ChangeMakerFilter $changeMakerFilter
     * @return Response
     */
    public function changeMakerFilter(Request $request, ChangeMakerFilter $changeMakerFilter): Response
    {
        $articles = $changeMakerFilter->filterChangeMaker($request, $this->getParameter('company_id'));
        return $this->render('portal/change-maker/_ajax_changemakes.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/change-makers/{articleSlugName}", name="change-maker-detail", methods={"GET", "POST"})
     * @param Request $request
     * @param CmsArticle $cmsArticle
     * @return Response
     */
    public function changeMakerDetail(Request $request, CmsArticle $cmsArticle): Response
    {
        $cmsContent = $this->getDoctrine()->getRepository(CmsPage::class)->getContentByPage('change-makers');
        // Added view Counter
        $entityManager = $this->getDoctrine()->getManager();
        $existingViewCount = $cmsArticle->getArticleViewCount();
        if ($existingViewCount == '')
        {
            $updateViewCount = 1;

        } else {
            $updateViewCount = ($existingViewCount + 1);
        }
        $cmsArticle->setArticleViewCount($updateViewCount);
        $entityManager->persist($cmsArticle);
        $entityManager->flush();

        $cmsArticleComment = new CmsArticleComment();
        $form = $this->createForm(CmsUserCommentType::class, $cmsArticleComment);
        $form->handleRequest($request);

        return $this->render('portal/change-maker/change-maker-detail.html.twig', [
            'article' => $cmsArticle,
            'form' => $form->createView(),
            'cmsContent' => $cmsContent,
            'reply_form' => $this->createForm(CmsUserCommentType::class, $cmsArticleComment),

        ]);
    }

    /**
     * @Route("/blog", name="blog", methods={"GET", "POST"})
     */
    public function blog(): Response
    {
        $blogContent = $this->getDoctrine()->getRepository(CmsPage::class)->getContentByPage('blog');

//        dd($blogContent->getCmsPageContent()[0]);

        $yearList = $this->getDoctrine()->getRepository(CmsArticle::class)->getYearList($this->getParameter('company_id'), 1);
        $monthList = $this->getDoctrine()->getRepository(CmsArticle::class)->getMonthList($this->getParameter('company_id'), 1);

        $monthArray = array();
        foreach ($monthList as $key=>$month) {

            $monthNum = $month['month'];
            if(strlen($monthNum) == 1) {
                $monthNum = '0'.$monthNum;
            }

            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');

            $monthArray[$key]['month'] = $monthNum;
            $monthArray[$key]['monthName'] = $monthName;
        }

        return $this->render('portal/page/blog.html.twig', [
            'yearList' => $yearList,
            'monthList' => $monthArray,
            'blog' => $blogContent
        ]);
    }

    /**
     * @Route("/blog/filter", name="blog-filter", methods={"GET", "POST"})
     * @param Request $request
     * @param ChangeMakerFilter $changeMakerFilter
     * @return Response
     */
    public function blogFilter(Request $request, ChangeMakerFilter $changeMakerFilter): Response
    {
//dd($request);

        $articles = $changeMakerFilter->filterBlog($request, $this->getParameter('company_id'));

        return $this->render('portal/page/_ajax_blog.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/blog/{articleSlugName}", name="blog-detail", methods={"GET", "POST"})
     * @param Request $request
     * @param CmsArticle $cmsArticle
     * @return Response
     */
    public function blogDetail(Request $request, CmsArticle $cmsArticle): Response
    {
        $blogContent = $this->getDoctrine()->getRepository(CmsPage::class)->getContentByPage('blog');

        // Added view Counter
        $entityManager = $this->getDoctrine()->getManager();
        $existingViewCount = $cmsArticle->getArticleViewCount();
        if ($existingViewCount == '')
        {
            $updateViewCount = 1;

        } else {
            $updateViewCount = ($existingViewCount + 1);
        }
        $cmsArticle->setArticleViewCount($updateViewCount);
        $entityManager->persist($cmsArticle);
        $entityManager->flush();

        /*
$cmsArticleComment = $this->getDoctrine()->getRepository(CmsArticleComment::class)->findBy([
    'cmsArticle' => 18,
    'isApproved' => 1],
    ['commentDateTime' => 'DESC']);

//dd($cmsArticleComment[0]->getAppUser()->getAppUserInfo()->getUserAvatarImage());
        */

        $cmsArticleComment = new CmsArticleComment();
        $form = $this->createForm(CmsUserCommentType::class, $cmsArticleComment);
        $form->handleRequest($request);
        return $this->render('portal/page/blogdetail.html.twig', [
            'article' => $cmsArticle,
            'form' => $form->createView(),
            'reply_form' => $this->createForm(CmsUserCommentType::class, $cmsArticleComment),
            'blog' => $blogContent
        ]);
    }

    /**
     * @Route("/article/like", name="article-like", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function articleLike(Request $request): Response
    {
        $articleId = trim($request->query->get('id'));
        $articleLike = $this->getDoctrine()->getRepository(CmsArticle::class)->find($articleId);

        if (!empty($articleId)) {
            $updateCount = ($articleLike->getArticleLikeCount() + 1);
            $articleLike->setArticleLikeCount($updateCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($articleLike);
            $entityManager->flush();
            return $this->json(['count' => $updateCount]);
        }
        else {
            return $this->json(['count' => $articleLike->getArticleLikeCount()]);
        }

    }

    /**
     * @Route("/article/share", name="article-share", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function articleShare(Request $request): Response
    {
        $articleId = trim($request->query->get('id'));
        $articleShare = $this->getDoctrine()->getRepository(CmsArticle::class)->find($articleId);

        if (!empty($articleId)) {
            $updateCount = ($articleShare->getArticleShareCount() + 1);
            $articleShare->setArticleShareCount($updateCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($articleShare);
            $entityManager->flush();
            return $this->json(['count' => $updateCount]);
        }
        else {
            return $this->json(['count' => $articleShare->getArticleShareCount()]);
        }

    }

    /**
     * @Route("/comment/like", name="comment-like", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function commentLike(Request $request): Response
    {
        $commentId = trim($request->query->get('id'));
        $commentLike = $this->getDoctrine()->getRepository(CmsArticleComment::class)->find($commentId);

        if (!empty($commentId)) {
            $updateCount = ($commentLike->getCommentLikeCount() + 1);
            $commentLike->setCommentLikeCount($updateCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentLike);
            $entityManager->flush();
            return $this->json(['count' => $updateCount]);
        }
        else {
            return $this->json(['count' => $commentLike->getCommentLikeCount()]);
        }

    }

    /**
     * @Route("/comment", name="comment", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function comment(Request $request): Response
    {
        $successmsg = '';
        $articleId = trim($request->request->get('articleId'));
        $commentId = trim($request->request->get('commentId'));
        $articleComment = strip_tags($request->request->get('articleComment'));
        //$commentorName = strip_tags($request->request->get('commentorName'));
        $commentorName = $this->getUser()->getUsername();
        //$appUserId = $this->getUser()->getId();
        //$commentorEmail = strip_tags($request->request->get('commentorEmail'));
        //$commentorWebsite = strip_tags($request->request->get('commentorWebsite'));

        $article = $this->getDoctrine()->getRepository(CmsArticle::class)->find($articleId);
        if (!$article) {
            return $this->json(['message' => 'No Article found for the submitted comment']);
        } else {

        $cmsArticleComment = new CmsArticleComment();
        $cmsArticleComment->setCmsArticle($article);
        if (!empty($commentId)) {
            $cmsArticleComment->setParentComment($this->getDoctrine()->getRepository(CmsArticleComment::class)->find($commentId));
        }
        $cmsArticleComment->setArticleComment($articleComment);
        $cmsArticleComment->setCommentorName($commentorName);
        if (!empty($this->getUser())) {
            $cmsArticleComment->setAppuser($this->getUser());
        }
        //$cmsArticleComment->setCommentorEmail($commentorEmail);
        //$cmsArticleComment->setCommentorWebsite($commentorWebsite);
        $cmsArticleComment->setIsApproved(0);
        $cmsArticleComment->setIsActive(0);
        $cmsArticleComment->setCommentDateTime(new DateTime('now'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($cmsArticleComment);
        $entityManager->flush();
            if (!empty($commentId)) {
                $successmsg = 'Your reply has been successfully posted.';
            } else {
                $successmsg = 'Your comment has been successfully posted.';
            }
            return $this->json(['message' => $successmsg]);
        }
    }

    /**
     * @Route("/thank-you", name="thank-you", methods={"GET", "POST"})
     */
    public function thankYou(): Response
    {
        return $this->render('portal/page/thankyou.html.twig', [
        ]);
    }


    /**
     * @Route("/faqs", name="faqs", methods={"GET", "POST"})
     */
    public function faq(): Response
    {
        $faqs = $this->getDoctrine()->getRepository(CmsFaq::class)->findBy(['isActive' => 1, 'orgCompany' => $this->getParameter('company_id')]);
        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'faqs', 'orgCompany' => $this->getParameter('company_id')]);

        return $this->render('portal/page/faq.html.twig', [
            'faq' => $faqs,
            'page_content' => $pageContent
        ]);
    }

    /**
     * @Route("/change-makers-project-list", name="change-makers-project-list", methods={"GET", "POST"})
     * @param Request $request
     * @param ChangeMakerFilter $changeMakerFilter
     * @param SessionInterface $session
     * @return Response
     */
    public function changeMakersProjectList(Request $request, ChangeMakerFilter $changeMakerFilter, SessionInterface $session): Response
    {
        $changeMakerUserId = '';
        if($session->has('changeMakerUserId')) {
            // used to get projects of user id, this will be set from change makers detail page
            $changeMakerUserId = $session->get('changeMakerUserId');
            $session->remove('changeMakerUserId');
        }

        $areaInterests = $this->getDoctrine()->getRepository(TrnCircle::class)->getPrimaryAreaInterestList($this->getParameter('company_id'), true);

        $arrMstHighLights = $this->getDoctrine()->getRepository(MstHighlights::class)->findBy(['isActive' => 1]);
        $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);

        if(isset($changeMakerUserId) && $changeMakerUserId != '') {
            $arrCircleList =  $changeMakerFilter->filterUserProjects($objMstStatus, $changeMakerUserId,$this->getParameter('company_id'));
        } else {
            $arrCircleList =  $changeMakerFilter->filterProjects($objMstStatus, $request,$this->getParameter('company_id'));
        }
        //$arrCircleList =  $trnCircleRepository->getAllCircles($this->getParameter('company_id'), $objMstStatus);
        $currentDateTime = new DateTime();
        return $this->render('portal/change-maker/change-makers-projects.html.twig', [
            'areaInterests' => $areaInterests,
            'arrCircleList' => $arrCircleList,
            'currentDateTime' => $currentDateTime,
            'arrMstHighLights' => $arrMstHighLights
        ]);
    }

    /**
     * @Route("/project-details/{id}", name="project-details", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param SessionInterface $session
     * @param ProjectService $projectService
     * @return Response
     */
    public function projectDetails(Request $request, TrnCircle $trnCircle, SessionInterface $session,
                                   ProjectService $projectService): Response
    {
        $arrMstEventProductType = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy(["isActive" => true]);

        $returnArr = $projectService->getGoodnessTimeLineDetails($trnCircle, false,true, 3);
        $goodnessList = $returnArr['goodnessDetails'];

        $trnCircleEventComments = new TrnCircleEventComments();
        $form = $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments);
        $form->handleRequest($request);
        //$projectService->makeEntryForVisitor($trnCircle);

        return $this->render('portal/change-maker/projects-details.html.twig', [
            'circle' => $trnCircle,
            'arrMstEventProductType' => $arrMstEventProductType,
            'form' => $form->createView(),
            'reply_form' => $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments),
            'goodnessDetails' => $goodnessList,
        ]);
    }

    /**
     * @Route("/project-goodness-timeline/{id}", name="project-goodness-timeline", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param ProjectService $projectService
     * @return Response
     */
    public function projectGoodnessTimeline(Request $request, TrnCircle $trnCircle, ProjectService $projectService): Response
    {
        $returnArr = $projectService->getGoodnessTimeLineDetails($trnCircle, true,false, 0);
        $goodnessList = $returnArr['goodnessDetails'];
        $yearArray = $returnArr['yearArray'];

        return $this->render('portal/change-maker/project-goodness-timeline.html.twig', [
            'circle' => $trnCircle,
            'goodnessDetails' => $goodnessList,
            'yearArray' => $yearArray
        ]);
    }

    /**
     * @Route("/project-gallery/{id}", name="project-gallery", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @return Response
     */
    public function projectGallery(Request $request, TrnCircle $trnCircle): Response
    {
        return $this->render('portal/change-maker/project-gallery.html.twig', [
            'circle' => $trnCircle,
        ]);
    }

    /**
     * @Route("/change-makers-project-list/filter", name="change-makers-project-list-filter", methods={"GET", "POST"})
     * @param Request $request
     * @param ChangeMakerFilter $changeMakerFilter
     * @return Response
     */
    public function changeMakersProjectListFilter(Request $request, ChangeMakerFilter $changeMakerFilter): Response
    {
        $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $arrCircleList =  $changeMakerFilter->filterProjects($objMstStatus, $request,$this->getParameter('company_id'));
        return $this->render('portal/change-maker/_ajax_change-makers-projects.html.twig', [
            'arrCircleList' => $arrCircleList
        ]);
    }

    /**
     * @Route("/change-makers-project-list/lazy-load", name="change-makers-project-list-lazy-load", methods={"GET",
     *     "POST"})
     * @param Request $request
     * @param ChangeMakerFilter $changeMakerFilter
     * @return Response
     */
    public function changeMakersProjectListLazyLoad(Request $request, TrnCircleRepository $trnCircleRepository): Response
    {
        /*$articles = $changeMakerFilter->filterChangeMaker($request, $this->getParameter('company_id'));
        return $this->render('portal/change-maker/_ajax_changemakes.html.twig', [
            'articles' => $articles,
        ]);*/

    }

    /**
     * @Route("/project-event-details/{id}", name="project-event-details", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function projectEventDetails(Request $request, TrnCircleEvents $trnCircleEvents): Response
    {
        $arrMstEventProductType = $this->getDoctrine()->getRepository(MstEventProductType::class)->findAll(["isActive" => true]);

        $trnCircleEventComments = new TrnCircleEventComments();
        $form = $this->createForm(TrnCircleEventCommentsType::class, $trnCircleEventComments);
        $form->handleRequest($request);

        return $this->render('portal/change-maker/projects-event-details.html.twig', [
            'eventData' => $trnCircleEvents,
            'arrMstEventProductType' => $arrMstEventProductType,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/circle/like", name="circle-like", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function circleLike(Request $request): Response
    {
        $circleId = trim($request->query->get('id'));
        $objCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circleId);
        if (!empty($objCircle)) {
            $nLikeCount = 0;
            if(!empty($objCircle->getLikeCount()))
                $nLikeCount = $objCircle->getLikeCount();
            $nLikeCount++;
            $objCircle->setLikeCount($nLikeCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($objCircle);
            $entityManager->flush();
            return $this->json(['count' => $nLikeCount]);
        }
        else {
            return $this->json(['count' => 0]);
        }
    }

    /**
     * @Route("/circle/comment/like", name="circle-comment-like", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function circleCommentLike(Request $request): Response
    {
        $commentId = trim($request->query->get('id'));
        $commentLike = $this->getDoctrine()->getRepository(TrnCircleEventComments::class)->find($commentId);

        if (!empty($commentId)) {
            $updateCount = ($commentLike->getLikeCount() + 1);
            $commentLike->setLikeCount($updateCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentLike);
            $entityManager->flush();
            return $this->json(['count' => $updateCount]);
        }
        else {
            return $this->json(['count' => $commentLike->getLikeCount()]);
        }

    }

    /**
     * @Route("/event/comment/like", name="event-comment-like", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function eventCommentLike(Request $request): Response
    {
        $commentId = trim($request->query->get('id'));
        $commentLike = $this->getDoctrine()->getRepository(TrnCircleEventComments::class)->find($commentId);

        if (!empty($commentId)) {
            $updateCount = ($commentLike->getLikeCount() + 1);
            $commentLike->setLikeCount($updateCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentLike);
            $entityManager->flush();
            return $this->json(['count' => $updateCount]);
        }
        else {
            return $this->json(['count' => $commentLike->getLikeCount()]);
        }

    }


    /**
     * @Route("/circle/share", name="circle-share", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function circleShare(Request $request): Response
    {
        $circleId = trim($request->query->get('id'));
        $circleShare = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circleId);

        if (!empty($circleId)) {
            $updateCount = ($circleShare->getShareCount() + 1);
            $circleShare->setShareCount($updateCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($circleShare);
            $entityManager->flush();
            return $this->json(['count' => $updateCount]);
        }
        else {
            return $this->json(['count' => $circleShare->getShareCount()]);
        }

    }

    /**
     * @Route("/event/share", name="event-share", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function eventShare(Request $request): Response
    {
        $eventId = trim($request->query->get('id'));
        $eventShare = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($eventId);

        if (!empty($eventId)) {
            $updateCount = ($eventShare->getShareCount() + 1);
            $eventShare->setShareCount($updateCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eventShare);
            $entityManager->flush();
            return $this->json(['count' => $updateCount]);
        }
        else {
            return $this->json(['count' => $eventShare->getShareCount()]);
        }

    }

    /**
     * @Route("/event/like", name="event-like", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function eventLike(Request $request): Response
    {
        $eventId = trim($request->query->get('id'));
        $objEvent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($eventId);
        if (!empty($objEvent)) {
            $nLikeCount = 0;
            if(!empty($objEvent->getLikeCount()))
                $nLikeCount = $objEvent->getLikeCount();
            $nLikeCount++;
            $objEvent->setLikeCount($nLikeCount);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($objEvent);
            $entityManager->flush();
            return $this->json(['count' => $nLikeCount]);
        }
        else {
            return $this->json(['count' => 0]);
        }
    }

    /**
     * @Route("/get-latest-circle-events/filter", name="get-latest-circle-events-filter", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function getLatestCircleEventsFilter(Request $request, ChangeMakerFilter $changeMakerFilter): Response
    {
        $em = $this->getDoctrine();
        $objMstStatus = $em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $arrEventList =  $changeMakerFilter->filterProjectsEvents($objMstStatus, $request,$this->getParameter('company_id'));
        return $this->render('portal/change-maker/_ajax_projects-event-listing.html.twig', [
            'arrEventList' => $arrEventList
        ]);

    }

    /**
     * @Route("/circle-comment", name="circle-comment", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function circleComment(Request $request): Response
    {
        $circleId = trim($request->request->get('circleId'));
        $commentId = trim($request->request->get('commentId'));
        $comment = strip_tags($request->request->get('comment'));
        $commentorName = $this->getUser()->getUsername();
        /*$commentorName = strip_tags($request->request->get('commentorName'));
        $commentorEmail = strip_tags($request->request->get('commentorEmail'));
        $commentorWebsite = strip_tags($request->request->get('commentorWebsite'));*/
        $objCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circleId);
        if (!$objCircle) {
            return $this->json(['message' => 'No Project found for the submitted comment']);
        } else {
            $trnCircleEventComments = new TrnCircleEventComments();
            $trnCircleEventComments->setTrnCircle($objCircle);
            if (!empty($commentId)) {
                $trnCircleEventComments->setParentComment($this->getDoctrine()->getRepository(TrnCircleEventComments::class)->find($commentId));
            }
            $trnCircleEventComments->setComment($comment);
            $trnCircleEventComments->setCommentorName($commentorName);

            if (!empty($this->getUser())) {
                $trnCircleEventComments->setAppuser($this->getUser());
            }
            /*$trnCircleEventComments->setCommentorEmail($commentorEmail);
            $trnCircleEventComments->setCommentorWebsite($commentorWebsite);*/
            $trnCircleEventComments->setIsApproved(0);
            $trnCircleEventComments->setIsActive(0);
            $trnCircleEventComments->setCommentDateTime(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trnCircleEventComments);
            $entityManager->flush();
            return $this->json(['message' => 'Your comment has been successfully posted.']);
        }
    }

    /**
     * @Route("/project/request-to-join/{id}", name="circle-request-to-join", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param MyAccountService $myAccountService
     * @param NotificationService $notificationService
     * @return Response
     */
    public function requestToJoin(Request $request, SessionInterface $session, MyAccountService $myAccountService,
                                  NotificationService $notificationService): Response
    {
        $resultArr = array();
        $circleId = trim($request->get('id'));

        if(!$this->getUser())
        {
            // not a logged in user
            $this->addFlash('requestjoinerror', 'Please login to join');
            $returnUrl = $this->generateUrl('circle-request-to-join', ['id' => $circleId]);
            $session->set('returnToRequest', $returnUrl);

            return $this->redirectToRoute('login-email');
        } else {
            // if user was not logged in and went from this request, get him back to request again
            if($session->has('returnToRequest')) {
                $session->remove('returnToRequest');
            }
        }

        $objCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circleId);

        if (!empty($objCircle)) {
            $entityManager = $this->getDoctrine()->getManager();
            $appUser = $this->getUser();
            $appUserInfo = $this->getUser()->getAppUserInfo();

            $trnCircleRequestToJoinExist = $this->getDoctrine()
                ->getRepository(TrnCircleRequestToJoin::class)
                ->findOneBy(array('trnCircle' => $objCircle, 'appUser' => $this->getUser(), 'isActive' => 1));

            if (empty($trnCircleRequestToJoinExist)) {
                $objTrnCircleRequestToJoin = new TrnCircleRequestToJoin();
                $objTrnCircleRequestToJoin->setAppUser($this->getUser());
                if ($objCircle->getMstJoinBy()->getJoinBy() == 'Open')
                    $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Activated']));
                else
                    $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Pending Activation']));

                $objTrnCircleRequestToJoin->setFirstName($appUserInfo->getUserFirstName());
                $objTrnCircleRequestToJoin->setLastName($appUserInfo->getUserLastName());
                $objTrnCircleRequestToJoin->setEmailAddress($appUserInfo->getUserEmail());
                $objTrnCircleRequestToJoin->setMobileCountryCode($appUserInfo->getMobileCountryCode());
                $objTrnCircleRequestToJoin->setMobileNumber($appUserInfo->getUserMobileNumber());
                $objTrnCircleRequestToJoin->setMstCity($appUserInfo->getMstCity());
                $objTrnCircleRequestToJoin->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnCircleRequestToJoin->setIsActive(1);
                $objTrnCircleRequestToJoin->setUserIpAddress($_SERVER['SERVER_ADDR']);
                $objTrnCircleRequestToJoin->setRequestOn(new \DateTime());
                $objTrnCircleRequestToJoin->setTrnCircle($objCircle);
                $entityManager->persist($objTrnCircleRequestToJoin);
                $entityManager->flush();
                $resultArr['status'] = 'request added';
                if ($objCircle->getMstJoinBy()->getJoinBy() != 'Open') {
                    //Project Request to Join Creator
                    $notificationService->setAppUser($objCircle->getAppUser());
                    $notificationService->setTrnCircle($objCircle);
                    $notificationService->setRequesterAppUser($appUser);
                    $notificationService->doProcess('Project Request to Join Creator');

                    //Project Request to Join Member
                    $notificationService->setAppUser($appUser);
                    $notificationService->setTrnCircle($objCircle);
                    $notificationService->setRequesterAppUser($appUser);
                    $notificationService->doProcess('Project Request to Join Member');
                }
                $this->addFlash('requestjoinsuccess', 'Your request is under process');
            }
            else {
                $resultArr['status'] = 'request already added';
            }
            if ($objCircle->getMstJoinBy()->getJoinBy() == 'Open')
                $myAccountService->makeEntryForCollectionCentre($appUser, $objCircle);
        }
        else {
            // invalid project
            $resultArr['status'] = 'Invalid Project';
        }

        return $this->redirectToRoute('project-details', ['id'=>$circleId]);
    }

    /**
     * @Route("/event-donate-before-login/{event}", name="event-donate-before-login", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param MyAccountService $myAccountService
     * @param ProjectService $projectService
     * @return Response
     */
    public function eventDonateBeforeLogin(Request $request, SessionInterface $session, MyAccountService $myAccountService, ProjectService $projectService): Response
    {
        $eventId = trim($request->get('event'));

        if($request->get('processType') != null && $request->get('processType') != '') {
            // if participate / contribute value set then get the same
            $processType = $request->get('processType');
        } else {
            // default is donate
            $processType = 'donate';
        }

        if($session->has('returnToRequest') || $this->getUser() != null) {

            /*if($session->has('returnToRequest')) {
                $usercamefromlogin = true;
            } else {
                $usercamefromlogin = false;
            }*/

            $objEvent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($eventId);
            $objCircle = $objEvent->getTrnCircle();

            $trnCircleRequestToJoinExist = $this->getDoctrine()
                ->getRepository(TrnCircleRequestToJoin::class)
                ->findOneBy(array('trnCircle' => $objCircle, 'appUser' => $this->getUser(), 'isActive' => 1));

            $session->remove('returnToRequest');

            $entityManager = $this->getDoctrine()->getManager();
            $appUser = $this->getUser();
            $appUserInfo = $this->getUser()->getAppUserInfo();

            if($objEvent->getTrnCircle()->getMstJoinBy()->getJoinBy() == 'Closed') {
                if(empty($trnCircleRequestToJoinExist)) {

                    // make him join and throw to project list with appropriate msg
                    $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Pending Activation']);
                    $objOrgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
                    $projectService->makeEntryForRequestToJoin($appUser, $objCircle, $objMstStatus, $objOrgCompany);

                    $resultArr['status'] = 'request added';
                    $this->addFlash('requestjoinsuccess', 'Your join request is under process');

                    return $this->redirectToRoute('project-details', ['id' => $objCircle->getId()]);

                } else {

                    $forwardPageRoute = 'fund-details';
                    if($processType == 'participate') {
                        $forwardPageRoute = 'volunteer-details';
                    } elseif ($processType == 'contribute') {
                        $forwardPageRoute = 'material-details';
                    }

                    return $this->redirectToRoute($forwardPageRoute, ['id' => $eventId]);
                    // throw to fund-details to donate
                }
            } else {
                if(empty($trnCircleRequestToJoinExist)) {
                    // make him join and throw to throw to fund-details to donate as no approval required

                    $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Activated']);
                    $objOrgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
                    $projectService->makeEntryForRequestToJoin($appUser, $objCircle, $objMstStatus, $objOrgCompany);

                    // as open type project, add entry to collection center
                    $myAccountService->makeEntryForCollectionCentre($appUser, $objCircle);

                    $resultArr['status'] = 'request added';

                    $forwardPageRoute = 'fund-details';
                    if($processType == 'participate') {
                        $forwardPageRoute = 'volunteer-details';
                    } elseif ($processType == 'contribute') {
                        $forwardPageRoute = 'material-details';
                    }

                    $session->set('nowJoined' , 'true');
                    return $this->redirectToRoute($forwardPageRoute, ['id' => $eventId]);

                } else {
                    // throw to fund-details to donate

                    $forwardPageRoute = 'fund-details';
                    if($processType == 'participate') {
                        $forwardPageRoute = 'volunteer-details';
                    } elseif ($processType == 'contribute') {
                        $forwardPageRoute = 'material-details';
                    }

                    return $this->redirectToRoute($forwardPageRoute, ['id' => $eventId]);
                }
            }
        }

        $returnUrl = '';
        $currUrl = $this->generateUrl('event-donate-before-login', ['event' => $eventId, 'processType' => $processType], UrlGeneratorInterface::ABSOLUTE_URL );

        $joinError = 'Please login to donate';
        if($processType == 'participate') {
            $joinError = 'Please login to participate';
        } elseif ($processType == 'contribute') {
            $joinError = 'Please login to contribute';
        }

        $this->addFlash('requestjoinerror', $joinError);
        $session->set('returnToRequest', $currUrl);

        return $this->redirectToRoute('login-email');
    }

    /**
     * @Route("/donate-before-login/{circle}/{type}", name="donate-before-login", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param MyAccountService $myAccountService
     * @param ProjectService $projectService
     * @return Response
     */
    public function donateBeforeLogin(Request $request, SessionInterface $session,
                                      MyAccountService $myAccountService, ProjectService $projectService): Response
    {
        if($session->has('forwardUrl') && !empty($this->getUser())) {
//dd($session);
            $circleId = $session->get('donateCircleId');
            $forwardUrl = $session->get('forwardUrl');
            $circleType = $session->get('donateCircleType');

            $resultStatus = '';

            if ($circleType == 'Open') {
                $objCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circleId);

                if (!empty($objCircle)) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $appUser = $this->getUser();
                    $appUserInfo = $this->getUser()->getAppUserInfo();

                    $trnCircleRequestToJoinExist = $this->getDoctrine()
                        ->getRepository(TrnCircleRequestToJoin::class)
                        ->findOneBy(array('trnCircle' => $objCircle, 'appUser' => $this->getUser()));

                    if (empty($trnCircleRequestToJoinExist)) {

                        if ($objCircle->getMstJoinBy()->getJoinBy() == 'Open') {
                            $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                            => 'Activated']);
                        } else {
                            $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                            => 'Pending Activation']);
                        }
                        $objOrgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
                        $projectService->makeEntryForRequestToJoin($appUser, $objCircle, $objMstStatus, $objOrgCompany);
                        $resultArr['status'] = 'request added';

                        //$this->addFlash('requestjoinsuccess', 'Your request is under process');
                    }
                    else {
                        $resultArr['status'] = 'request already added';
                    }
                    if ($objCircle->getMstJoinBy()->getJoinBy() == 'Open')
                        $myAccountService->makeEntryForCollectionCentre($appUser, $objCircle);
                }
                else {
                    // invalid project
                    $resultArr['status'] = 'Invalid Project';
                }
            }

            $session->remove('donateCircleType');
            $session->remove('donateCircleId');
            $session->remove('returnToRequest');
            $session->remove('forwardUrl');

            return $this->redirect($forwardUrl);
        }

        $returnUrl = '';

        $circleId = trim($request->get('circle'));
        $circleType = trim($request->get('type'));

        if($request->query->has('project_name')) {
            $projectName = trim($request->query->get('project_name'));
            $returnUrl = $this->generateUrl('event-listing', ['project_name' => $projectName], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $eventId = trim($request->query->get('id'));
            $returnUrl = $this->generateUrl('event-details', ['id' => $eventId], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $currUrl = $this->generateUrl('donate-before-login', ['circle' => $circleId, 'type' => $circleType], UrlGeneratorInterface::ABSOLUTE_URL );

        $this->addFlash('requestjoinerror', 'Please login to donate');
        $session->set('donateCircleType', $circleType);
        $session->set('donateCircleId', $circleId);
        $session->set('returnToRequest', $currUrl);
        $session->set('forwardUrl', $returnUrl);

        return $this->redirectToRoute('login-email');
    }

    /**
     * @Route("/donate-after-login/{circle}/{type}", name="donate-after-login", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param MyAccountService $myAccountService
     * @param ProjectService $projectService
     * @return Response
     */
    public function donateAfterLogin(Request $request, SessionInterface $session,
                                     MyAccountService $myAccountService, ProjectService $projectService): Response
    {
        if($session->has('forwardUrl')) {
//dd($session);
            $circleId = $session->get('donateCircleId');
            $forwardUrl = $session->get('forwardUrl');
            $circleType = $session->get('donateCircleType');

            $resultStatus = '';

            if ($circleType == 'Open') {
                $objCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circleId);

                if (!empty($objCircle)) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $appUser = $this->getUser();
                    $appUserInfo = $this->getUser()->getAppUserInfo();

                    $trnCircleRequestToJoinExist = $this->getDoctrine()
                        ->getRepository(TrnCircleRequestToJoin::class)
                        ->findOneBy(array('trnCircle' => $objCircle, 'appUser' => $this->getUser()));

                    if (empty($trnCircleRequestToJoinExist)) {

                        if ($objCircle->getMstJoinBy()->getJoinBy() == 'Open') {
                            $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                            => 'Activated']);
                        } else {
                            $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                            => 'Pending Activation']);
                        }
                        $objOrgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
                        $projectService->makeEntryForRequestToJoin($appUser, $objCircle, $objMstStatus, $objOrgCompany);

                        $resultArr['status'] = 'request added';

                        //$this->addFlash('requestjoinsuccess', 'Your request is under process');
                    }
                    else {
                        $resultArr['status'] = 'request already added';
                    }
                    if ($objCircle->getMstJoinBy()->getJoinBy() == 'Open')
                        $myAccountService->makeEntryForCollectionCentre($appUser, $objCircle);
                }
                else {
                    // invalid project
                    $resultArr['status'] = 'Invalid Project';
                }
            }

            $session->remove('donateCircleType');
            $session->remove('donateCircleId');
            $session->remove('returnToRequest');
            $session->remove('forwardUrl');

            return $this->redirect($forwardUrl);
        }

        $returnUrl = '';

        $circleId = trim($request->get('circle'));
        $circleType = trim($request->get('type'));

        if($request->query->has('project_name')) {
            $projectName = trim($request->query->get('project_name'));
            $returnUrl = $this->generateUrl('event-listing', ['project_name' => $projectName], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $eventId = trim($request->query->get('id'));
            $returnUrl = $this->generateUrl('event-details', ['id' => $eventId], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $currUrl = $this->generateUrl('donate-before-login', ['circle' => $circleId, 'type' => $circleType], UrlGeneratorInterface::ABSOLUTE_URL );

        $this->addFlash('requestjoinerror', 'Please login to donate');
        $session->set('donateCircleType', $circleType);
        $session->set('donateCircleId', $circleId);
        $session->set('returnToRequest', $currUrl);
        $session->set('forwardUrl', $returnUrl);

        return $this->redirectToRoute('login-email');
    }

    /**
     * @Route("/event-comment", name="event-comment", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function eventComment(Request $request): Response
    {
        $eventId = trim($request->request->get('eventId'));
        $commentId = trim($request->request->get('commentId'));
        $comment = strip_tags($request->request->get('comment'));
        $commentorName = $this->getUser()->getUsername();
        $successMsg = 'Your comment has been successfully posted.';

        $objEvent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($eventId);
        if (!$objEvent) {
            return $this->json(['message' => 'No Event found for the submitted comment']);
        } else {
            $trnCircleEventComments = new TrnCircleEventComments();
            $trnCircleEventComments->setTrnCircleEvents($objEvent);
            if (!empty($commentId)) {
                $trnCircleEventComments->setParentComment($this->getDoctrine()->getRepository(TrnCircleEventComments::class)->find($commentId));
                $successMsg = 'Your reply has been successfully posted.';
            }
            $trnCircleEventComments->setComment($comment);
            $trnCircleEventComments->setCommentorName($commentorName);

            if (!empty($this->getUser())) {
                $trnCircleEventComments->setAppuser($this->getUser());
            }
            $trnCircleEventComments->setIsApproved(0);
            $trnCircleEventComments->setIsActive(0);
            $trnCircleEventComments->setCommentDateTime(new DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trnCircleEventComments);
            $entityManager->flush();
            return $this->json(['message' => $successMsg]);
        }
    }

    /**
     * @Route("/contribute-to-a-change", name="contribute-to-a-change", methods={"GET", "POST"})
     * @param TrnCircleRepository $trnCircleRepository
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param ChangeMakerFilter $changeMakerFilter
     * @param Request $request
     * @return Response
     */
    public function contributeToAChange(TrnCircleRepository $trnCircleRepository, TrnCircleEventsRepository $trnCircleEventsRepository,
                                       ChangeMakerFilter $changeMakerFilter, Request $request): Response
    {
        $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $arrCircleList =  $trnCircleRepository->getLatestCircles($this->getParameter('company_id'), $objMstStatus);
        $em = $this->getDoctrine();
        $objMstStatus = $em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $arrEventList =  $trnCircleEventsRepository->getLatestEvents($objMstStatus, $this->getParameter('company_id'));
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $entityManager);
        return $this->render('portal/change-maker/contribute-to-change.html.twig', [
            'arrCircleList' => $arrCircleList,
            'arrEventList' => $arrEventList,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails
        ]);
    }

    /**
     * @Route("/be-a-change-maker", name="be-a-change-maker", methods={"GET", "POST"})
     * @return Response
     */
    public function beAChangeMaker(): Response
    {
//        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'be-a-change-maker-others']);
        return $this->render('portal/page/beachangemaker.html.twig', [
//            'page_content' => $pageContent
        ]);
    }


    /**
     * @Route("/get-inspired", name="get-inspired", methods={"GET", "POST"})
     * @return Response
     */

    public function getInspired(): Response
    {
//        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'get-inspired']);
        return $this->render('portal/page/getinspired/getinspired.html.twig', [
//            'page_content' => $pageContent
        ]);
    }

    /**
     * @Route("/volunteer-diaries", name="volunteer-diaries", methods={"GET", "POST"})
     */
    public function volunteerDiaries(): Response
    {
        /*$detect = new \Mobile_Detect();
        echo '<pre>HERE....';
        var_dump($detect->isMobile());
        var_dump($detect->isTablet());
        echo '</pre>';
        exit;*/


        // Volunteer Diaries Details
        $volunteer_articles = $this->getDoctrine()->getRepository(CmsArticle::class)->findBy(['mstArticleCategory' => 3,
            'orgCompany' => $this->getParameter('company_id'), 'isActive' => 1], ['sequenceNo' => 'ASC']);
        $total_volunteers = $this->getDoctrine()->getRepository(CmsArticle::class)->getArticleCount(3, 1);
        $cmsPage = $this->getDoctrine()->getRepository(CmsPage::class)->getContentBySlugName('volunteer-diaries');

        $areaInterests = $this->getDoctrine()->getRepository(CmsArticle::class)->getAreaInterestList(3, $this->getParameter('company_id'));

        return $this->render('portal/volunteer-diaries/volunteer-diaries-index.html.twig', [
            'area_interests' => $areaInterests,
            'volunteer_articles' => $volunteer_articles,
            'total_volunteers' => $total_volunteers,
            'cmsPage' => $cmsPage
        ]);

    }

    /**
     * @Route("/volunteer-diaries/filter", name="volunteer-diaries-filter", methods={"GET", "POST"})
     * @param Request $request
     * @param ChangeMakerFilter $changeMakerFilter
     * @return Response
     */
    public function volunteerDiariesFilter(Request $request, ChangeMakerFilter $changeMakerFilter): Response
    {
        $volunteer_articles = $changeMakerFilter->filterVolunteerDiaries($request, $this->getParameter('company_id'));
        return $this->render('portal/volunteer-diaries/_ajax_volunteer-diaries.html.twig', [
            'volunteer_articles' => $volunteer_articles,
        ]);
    }

    /**
     * @Route("/volunteer-diaries/{articleSlugName}", name="volunteer-diary-detail", methods={"GET", "POST"})
     * @param string $articleSlugName
     * @param CmsArticle $cmsArticle
     * @return Response
     */
    public function volunteerDiariesDetail(string $articleSlugName, CmsArticle $cmsArticle): Response
    {
        $cmsPage = $this->getDoctrine()->getRepository(CmsPage::class)->getContentBySlugName('volunteer-diaries');
        $volunteer_article = $this->getDoctrine()->getRepository(CmsArticle::class)->findOneBy(['articleSlugName'=>$articleSlugName,
            'mstArticleCategory' => 3, 'orgCompany' => $this->getParameter('company_id'), 'isActive' => 1]);
        $other_volunteers = $this->getDoctrine()->getRepository(CmsArticle::class)->getOtherArticlesBySlugName(3, $articleSlugName);
        $total_volunteers = $this->getDoctrine()->getRepository(CmsArticle::class)->getArticleCount(3, 1);

        return $this->render('portal/volunteer-diaries/volunteer-diaries-detail.html.twig', [
            'volunteer_article' => $volunteer_article,
            'other_volunteers' => $other_volunteers,
            'total_volunteers' => $total_volunteers,
            'cmsPage' => $cmsPage
        ]);
    }

    /**
     * @Route("/in-the-news", name="pressroom", methods={"GET", "POST"})
     * @return Response
     */

    public function inTheNews(): Response
    {
        $yearList = $this->getDoctrine()->getRepository(CmsPressRoom::class)->getYearList($this->getParameter('company_id'));
        $monthList = $this->getDoctrine()->getRepository(CmsPressRoom::class)->getMonthList($this->getParameter('company_id'));

        $monthArray = array();
        foreach ($monthList as $key=>$month) {

            $monthNum = $month['month'];
            if(strlen($monthNum) == 1) {
                $monthNum = '0'.$monthNum;
            }

            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');

            $monthArray[$key]['month'] = $monthNum;
            $monthArray[$key]['monthName'] = $monthName;
        }
        $newsList = $this->getDoctrine()->getRepository(CmsPressRoom::class)->findBy(['orgCompany' => $this->getParameter('company_id'),
            'isActive' => 1], ['articleDateTime' => 'DESC']);

        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'in-the-news', 'isActive' => 1, 'orgCompany' => $this->getParameter('company_id')]);

        return $this->render('portal/page/pressroom.html.twig', [
            'pageContent' => $pageContent,
            'newsList' => $newsList,
            'yearList' => $yearList,
            'monthList' => $monthArray
        ]);
    }

    /**
     * @Route("/in-the-news/filter", name="in-the-news-filter", methods={"GET", "POST"})
     * @param Request $request
     * @param NewsFilter $newsFilter
     * @return Response
     */
    public function inTheNewsFilter(Request $request, NewsFilter $newsFilter): Response
    {
        //dd($request->request->get('filterBy'));
        $newsList = $newsFilter->filterNews($request, $this->getParameter('company_id'));

        return $this->render('portal/page/_ajax_pressroom.html.twig', [
            'newsList' => $newsList,
        ]);
    }

    /**
     * @Route("/news-details/{newsId}", name="pressroom-detail", methods={"GET", "POST"})
     * @return Response
     */

    public function newsDetails(int $newsId): Response
    {
        $newsDetail = $this->getDoctrine()->getRepository(CmsPressRoom::class)->findOneBy(['id' => $newsId]);

        $pageContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'in-the-news', 'orgCompany' => $this->getParameter('company_id')]);

        return $this->render('portal/page/pressroomdetail.html.twig', [
            'newsDetail' => $newsDetail,
            'pageContent' => $pageContent
        ]);
    }

    /**
     * @Route("/team/{teamSlugName}", name="team", methods={"GET", "POST"})
     * @param string $teamSlugName
     * @return Response
     */
    public function team(string $teamSlugName): Response
    {
        $teamContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'the-team', 'slugName' => 'the-team', 'orgCompany' => $this->getParameter('company_id')]);

        return $this->render('portal/page/teamdetail.html.twig', [
            'team_content' => $teamContent
        ]);
    }

    /**
     * @Route("/our-inner-circle/{memberSlugName}", name="our-inner-circle", methods={"GET", "POST"})
     * @param string $memberSlugName
     * @return Response
     */
    public function ourInnerCircle(string $memberSlugName): Response
    {
        $innerCircleContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'our-inner-circle', 'slugName' => 'our-inner-circle', 'orgCompany' => $this->getParameter('company_id')]);

        return $this->render('portal/page/innercircledetail.html.twig', [
            'inner_circle_content' => $innerCircleContent
        ]);
    }

    /**
     * @Route("/testimonials", name="testimonials", methods={"GET", "POST"})
     */
    public function testimonials(): Response
    {
        $list = $this->getDoctrine()->getRepository(CmsUserTestimonial::class)->findBy(['orgCompany' => $this->getParameter('company_id'),
            'isActive' => 1], ['createDateTime' => 'DESC']);

        return $this->render('portal/page/testimonials.html.twig', [
            'list' => $list,
        ]);
    }

    /**
     * @Route("/create-project-tip", name="create-project-tip", methods={"GET", "POST"})
     */
    public function createProjectTip(): Response
    {
        return $this->render('portal/page/createprojecttip.html.twig', []);
    }

    /**
     * @Route("/create-event-tip", name="create-event-tip", methods={"GET", "POST"})
     */
    public function createEventTip(): Response
    {
        return $this->render('portal/page/createeventtip.html.twig', []);
    }

    /**
     * @Route("/show_project_events/{project}", name="show_project_events", methods={"GET","POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function showProjectEvents(Request $request, SessionInterface $session): Response
    {
        $project_id = $request->get('project');
        $session->set('project_id',$project_id);

        return $this->redirectToRoute('event-listing');
    }

    /**
     * @Route("/show_user_project/{user}", name="show_user_project", methods={"GET","POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function showUserProject(Request $request, SessionInterface $session): Response
    {
        $user_id = $request->get('user');

        $session->set('changeMakerUserId',$user_id);

        return $this->redirectToRoute('change-makers-project-list');
    }

    /**
     * @Route("/show_events_by_type/{type}", name="show_events_by_type", methods={"GET","POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function showEventsByType(Request $request, SessionInterface $session): Response
    {
        $event_type = $request->get('type');

        if($event_type == 'Volunteer') {
            $event_product_type = 'Volunteer (in Time)';
        }

        $session->set('event_product_type',$event_product_type);

        return $this->redirectToRoute('event-listing');
    }

    /**
     * @Route("/subscribe-to-us", name="subscribe-to-us", methods={"POST"})
     * @param Request $request
     * @param NotificationService $notificationService
     * @param CmsUserSubscriptionRepository $cmsUserSubscriptionRepository
     * @return JsonResponse
     */
    public function subscribeToUs(Request $request , NotificationService $notificationService,
                                  CmsUserSubscriptionRepository $cmsUserSubscriptionRepository) :JsonResponse
    {
        $strSubscriptionEmail = $request->get('emailId');
        $cmsUserSubscriptionExist = $cmsUserSubscriptionRepository->findOneBy(['userSubscriptionEmail' =>
            $strSubscriptionEmail]);
        if (!empty($cmsUserSubscriptionExist))
            return new JsonResponse(array('success' => 0, 'Message' => 'Already Subscribed'));
        $entityManager = $this->getDoctrine()->getManager();
        if (!empty($this->getUser())) {
            $appUser = $this->getUser();
            $nPreviousSubId = $appUser->getAppUserInfo()->getIsSubscribedToNewLetter();
            if (empty($nPreviousSubId)) {
                $appUser->getAppUserInfo()->setIsSubscribedToNewLetter(1);
                $this->addFlash('success', 'Successfully subscribed.');
                $entityManager->persist($appUser);
                $entityManager->flush();
                $notificationService->setAppUser($appUser);
                $notificationService->doProcess('Subscribe');
                $notificationService->doGCProcess('Subscribe');
            }
        }
        $objCmsUserSubscription = new CmsUserSubscription();

        if (!empty($this->getUser()))
            $objCmsUserSubscription->setAppUser($this->getUser());
        $objCmsUserSubscription->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find
        ($this->getParameter('company_id')));
        $objCmsUserSubscription->setIsSubscriptionActive(1);
        $objCmsUserSubscription->setSubscriptionDateTime(new \DateTime());
        if (!empty($this->getUser()))
            $objCmsUserSubscription->setUserSubscriptionEmail($this->getUser()->getAppUserInfo()->getUserEmail());
        else {
            $objCmsUserSubscription->setUserSubscriptionEmail($strSubscriptionEmail);
            $notificationService->sendSubscriptionEmailNotLoggedInUser($strSubscriptionEmail);
        }

        $entityManager->persist($objCmsUserSubscription);
        $entityManager->flush();

        return new JsonResponse(array('success' => 1, 'Message' => 'Successfully Subscribed'));
    }

    /**
     * @Route("/donate-to-gc", name="donate-to-gc", methods={"GET","POST"})
     * @param SessionInterface $session
     * @param Request $request
     * @return JsonResponse
     */
    public function donateToGc(Request $request, SessionInterface $session): JsonResponse
    {
        $amountToContribute = $request->get('amountToContribute');
        $changemakerarticleId = $request->get('changemakerarticleId');

        $session->set('amountToContribute', $amountToContribute);
        $session->set('changemakerarticleId', $changemakerarticleId);
        $session->get('expressDonate', 1);

        return new JsonResponse(array('success' => 1));
    }

    /**
     * @Route("/search-result", name="search-result")
     * @param Request $request
     * @param SearchHelper $searchHelper
     * @return Response
     */
    public function searchResult(Request $request, SearchHelper $searchHelper): Response
    {
        $searchText = $request->get('searchText');

//        dd($searchText);

        $company_id = $this->getParameter('company_id');
        $arrInputParam['searchText'] = $searchText;

        $changeMakers = $searchHelper->changeMakerListByName($searchText, $company_id);
        $projects = $searchHelper->projectListByName($searchText, $company_id);
        $events = $searchHelper->eventListByName($searchText, $company_id);
        $volunteers = $searchHelper->volunteerListByName($searchText, $company_id);

        $totalResultCount = count($changeMakers) + count($projects) + count($events) + count($volunteers);

        return $this->render('portal/page/searchresult.html.twig', [
            'changeMakers' => $changeMakers,
            'projects' => $projects,
            'events' => $events,
            'volunteers' => $volunteers,
            'totalResultCount' => $totalResultCount
        ]);
    }

}