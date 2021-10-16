<?php

namespace App\Controller\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Service\MyAccountService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnFundRaiserCircleEventDetails;
use App\Entity\Transaction\TrnVolunterCircleEventDetails;
use App\Entity\Transaction\TrnMaterialInKindCircleEventDetails;
use App\Form\Transaction\TrnCircleEventsEditType;
use App\Form\Transaction\TrnCircleEventsType;
use App\Form\Transaction\TrnCircleEventDistributeType;
use App\Form\Transaction\TrnCircleEventUploadedDocumentsType;
use App\Form\Transaction\TrnCircleEventUploadType;
use App\Form\Transaction\TrnFundRaiserCircleEventDetailsType;
use App\Form\Transaction\TrnVolunterCircleEventDetailsType;
use App\Form\Transaction\TrnMaterialInKindCircleEventDetailsType;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use App\Service\FileUploaderHelper;

/**
 * @Route("/core/product/event", name="product_event_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class TrnCircleEventController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET","POST"})
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param Request $request
     * @return Response
     */
    public function index(TrnCircleEventsRepository $trnCircleEventsRepository, Request $request): Response
    {
        $circleId = null;
        $arrSearch = array();
        $arrSearch['isActive'] = 1;
        if ($request->getMethod() === "POST") {
            $circleId = $nCircleId = $request->get('circleId');

            if (!empty($nCircleId)) {
                $trnCircle = $this->getDoctrine()->getManager()->getRepository(TrnCircle::class)->findBy(array( 'id'=>
                    $nCircleId, 'isActive' => 1));
                $arrSearch['trnCircle'] = $trnCircle;
            }
            $eventSearch = $request->get('eventSearch');
            if (!empty($eventSearch)) {
                $arrSearch['name'] = $eventSearch;
            }

            $textSearch = $request->get('textSearch');
            if (!empty($textSearch)) {
                $arrSearch['textSearch'] = $textSearch;
                $queryBuilder = $trnCircleEventsRepository->findByTextSearch($arrSearch);
            } else {
                $queryBuilder = $trnCircleEventsRepository->findBy($arrSearch);
            }

        } else {
            $queryBuilder = $trnCircleEventsRepository->findBy($arrSearch);
        }

        return $this->render('transaction/circle_event/index.html.twig', [
            'circle_events' => $queryBuilder,
            'path_index' => 'product_event_index',
            'path_add' => 'product_event_add',
            'path_edit' => 'product_event_edit',
            'path_show' => 'product_event_show',
            'label_title' => 'label.circle_event',
            'path_upload' => 'product_event_upload',
            'path_distribute_event' => 'product_event_distribute_event',
            'path_comment' => 'product_event_comment_index',
            'path_payment' => 'product_event_payment_index',
            'circleId' => $circleId
        ]);
    }
    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileUploaderHelper $fileUploaderHelper): Response
    {
        $trnCircleEvents = new TrnCircleEvents();
        $form = $this->createForm(TrnCircleEventsType::class, $trnCircleEvents);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trn_circle_events = $request->get('trn_circle_events');
            $entityManager = $this->getDoctrine()->getManager();
            if (!empty($trn_circle_events) && empty($trn_circle_events['trnCircle'])) {
                $newCircle = new TrnCircle();
                $strCircleName = 'Dummy Circle '.$trn_circle_events['name'];
                $newCircle->setCircle($strCircleName);
                $newCircle->setUserIpAddress($_SERVER['SERVER_ADDR']);
                $newCircle->setIsActive(1);
                $newCircle->setLocationLatitude($trn_circle_events['locationLatitude']);
                $newCircle->setLocationLongitude($trn_circle_events['locationLongitude']);
                $newCircle->setMstCity($trnCircleEvents->getMstCity());
                $newCircle->setMstState($trnCircleEvents->getMstState());
                $newCircle->setMstCountry($trnCircleEvents->getMstCountry());
                $newCircle->setAreaInterestPrimary($trnCircleEvents->getAreaInterestPrimary());
                $newCircle->setAreaInterestSecondary($trnCircleEvents->getAreaInterestSecondary());

                $newCircle->setMstStatus($trnCircleEvents->getMstStatus());
                $newCircle->setMstJoinBy($trnCircleEvents->getMstJoinBy());
                $newCircle->setCreatedOn(new \DateTime());
                $newCircle->setAppUser($trnCircleEvents->getAppUser());
                $newCircle->setOrgCompany($trnCircleEvents->getOrgCompany());
                $newCircle->setImpactStatement($trnCircleEvents->getEventPurpose());
                $newCircle->setHowGoalWillBeAchieved($trnCircleEvents->getHighlightsOfEvent());
                $newCircle->setcircleInformation('');

                $objOrg =  $trnCircleEvents->getAppUser()->getTrnOrganizationDetails();
                if( !empty($objOrg) && !empty($objOrg[0])) {
                    $arrTrnBankDetails = $objOrg[0]->getTrnBankDetails();
                    if(!empty($arrTrnBankDetails) && !empty($arrTrnBankDetails[0])) {
                        $objTrnBankDetails = $arrTrnBankDetails[0];
                        $newCircle->setBeneficiaryBankName($objTrnBankDetails->getBankName());
                        $newCircle->setBeneficiaryAccountHolderName($objTrnBankDetails->getAccountHolderName());
                        $newCircle->setBeneficiaryBankAccountNumber($objTrnBankDetails->getAccountNumber());
                        $newCircle->setBeneficiaryIfscCode($objTrnBankDetails->getIfscCode());
                        $newCircle->setMstBankAccountTypeBeneficiary($objTrnBankDetails->getMstBankAccountType());
                    }
                }
                $entityManager->persist($newCircle);
                $entityManager->flush();
                $trnCircleEvents->setTrnCircle($newCircle);
            }
            $trnCircleEvents->setCreatedOn(new \DateTime());
            $trnCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $entityManager->persist($trnCircleEvents);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('product_event_product_type', array('id' => $trnCircleEvents->getId()));
        }
        return $this->render('transaction/circle_event/form.html.twig', [
            'collection_centre' => $trnCircleEvents,
            'form' => $form->createView(),
            'label_title' => 'label.circle_event',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function show(TrnCircleEvents $trnCircleEvents): Response
    {
        return $this->render('transaction/circle_event/show.html.twig', [
            'data' => $trnCircleEvents,
            'label_title' => 'label.circle_event',
            'label_button' => 'label.update',
            'path_index' => 'product_event_index',
            'path_edit' => 'product_event_edit',
            'path_delete' => 'product_event_delete',
            'path_show' => 'product_event_show',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param FileUploaderHelper $fileUploaderHelper
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param NotificationService $notificationService
     * @return Response
     */
    public function edit(Request $request, TrnCircleEvents $trnCircleEvents, FileUploaderHelper $fileUploaderHelper,
                         TrnCircleEventsRepository $trnCircleEventsRepository, NotificationService
                         $notificationService, MyAccountService $myAccountService): Response
    {
        $form = $this->createForm(TrnCircleEventsEditType::class, $trnCircleEvents);
        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
        if ($form->isSubmitted()) {
            // isValid created issue for readonly form used for status change.

            $trn_circle_events_edit = $request->get('trn_circle_events_edit');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trnCircleEvents);
            $this->getDoctrine()->getManager()->flush();

            if($trnCircleEvents->getMstStatus() == 'Activated') {
                $notificationService->setAppUser($trnCircleEvents->getTrnCircle()->getAppUser());
                $notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
                $notificationService->setTrnCircleEvents($trnCircleEvents);
                $notificationService->doProcess('Event Activation');
            } elseif($trnCircleEvents->getMstStatus() == 'Rejected') {
                $notificationService->setAppUser($trnCircleEvents->getTrnCircle()->getAppUser());
                $notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
                $notificationService->setTrnCircleEvents($trnCircleEvents);
                $notificationService->doProcess('Event Rejected');
            } elseif($trnCircleEvents->getMstStatus() == 'Deactivated') {
                $notificationService->setAppUser($trnCircleEvents->getTrnCircle()->getAppUser());
                $notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
                $notificationService->setTrnCircleEvents($trnCircleEvents);
                $notificationService->doProcess('Event Deactivation Creator');
                $arrEventMemberListData = $myAccountService->getEventMemberList($trnCircleEvents);
                if (!empty($arrEventMemberListData) && !empty($arrEventMemberListData['participantData']) ){
                    foreach ($arrEventMemberListData['participantData'] as $participant) {
                        $notificationService->setAppUser($participant->getAppUser());
                        $notificationService->doProcess('Event Deactivation Member');
                    }
                }
            }

            //Check if any Child Event and Update the status accordingly.
            $arrChildTrnCircleEvents = $trnCircleEventsRepository->findBy(['parentTrnCircleEvents' => $trnCircleEvents]);
            foreach ($arrChildTrnCircleEvents as $childTrnCircleEvent) {
                $childTrnCircleEvent->setMstStatus($trnCircleEvents->getMstStatus());
                $entityManager->persist($childTrnCircleEvent);

                if($trnCircleEvents->getMstStatus() == 'Activated') {
                    $notificationService->setAppUser($childTrnCircleEvent->getTrnCircle()->getAppUser());
                    $notificationService->setTrnCircle($childTrnCircleEvent->getTrnCircle());
                    $notificationService->setTrnCircleEvents($childTrnCircleEvent);
                    $notificationService->doProcess('Event Activation');
                } elseif($trnCircleEvents->getMstStatus() == 'Rejected') {
                    $notificationService->setAppUser($childTrnCircleEvent->getTrnCircle()->getAppUser());
                    $notificationService->setTrnCircle($childTrnCircleEvent->getTrnCircle());
                    $notificationService->setTrnCircleEvents($childTrnCircleEvent);
                    $notificationService->doProcess('Event Rejected');
                } elseif($trnCircleEvents->getMstStatus() == 'Deactivated') {
                    $notificationService->setAppUser($childTrnCircleEvent->getTrnCircle()->getAppUser());
                    $notificationService->setTrnCircle($childTrnCircleEvent->getTrnCircle());
                    $notificationService->setTrnCircleEvents($childTrnCircleEvent);
                    $notificationService->doProcess('Event Deactivation Creator');
                    $arrEventMemberListData = $myAccountService->getEventMemberList($childTrnCircleEvent);
                    if (!empty($arrEventMemberListData) && !empty($arrEventMemberListData['participantData']) ){
                        foreach ($arrEventMemberListData['participantData'] as $participant) {
                            $notificationService->setAppUser($participant->getAppUser());
                            $notificationService->doProcess('Event Deactivation Member');
                        }
                    }
                }
            }
            $this->getDoctrine()->getManager()->flush();
            //Check if any Child Event and Update the status accordingly.

            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('product_event_index');
        }

        return $this->render('transaction/circle_event/edit_form.html.twig', [
            'data' => $trnCircleEvents,
            'form' => $form->createView(),
            'label_title' => 'label.circle_event',
            'label_button' => 'label.update',
            'mode' => 'edit',
            'path_index' => 'product_event_index',
            'path_edit' => 'product_event_edit',
            'path_delete' => 'product_event_delete',
            'path_show' => 'product_event_show',
        ]);

        /*$form = $this->createForm(TrnCircleEventsType::class, $trnCircleEvents);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $trnCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $user_id = $this->getUser()->getId();
            $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);
            $trnCircleEvents->setAppUser($appUser);
            $entityManager->persist($trnCircleEvents);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('product_event_product_type', array('id' => $trnCircleEvents->getId()));
        }

        return $this->render('transaction/circle_event/form.html.twig', [
            'collection_centre' => $trnCircleEvents,
            'form' => $form->createView(),
            'label_title' => 'label.circle_event',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);*/
    }

    /**
     * @Route("/product_type/{id}", name="product_type", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     * @throws Exception
     */
    public  function showNewProductType (TrnCircleEvents $trnCircleEvents, Request $request) {
        $arrEventProductType = array();
        foreach ($trnCircleEvents->getMstEventProductType() as $mstEventProductType){
            $arrEventProductType[] = $mstEventProductType->getEventProductType();
        }
        $nCurrentIndex = 0;
        if ($request->query->has('currentIndex')) {
            $nCurrentIndex = $request->get('currentIndex');
        }
        $strEventProductType =  str_ireplace(array("(", ")", " "), array("","",""), strtolower($arrEventProductType[$nCurrentIndex]));
        switch ($strEventProductType) {
            case 'volunteerintime': {
                $trnVolunterCircleEventDetailsArray = $trnCircleEvents->getTrnVolunterCircleEventDetails();
                $strLabel = 'label.update';
                if(!empty($trnVolunterCircleEventDetailsArray) && !empty($trnVolunterCircleEventDetailsArray[0])){
                    $trnVolunterCircleEventDetails = $trnVolunterCircleEventDetailsArray[0];
                }
                else{
                    $trnVolunterCircleEventDetails =  new TrnVolunterCircleEventDetails();
                    $trnVolunterCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $strLabel = 'label.create';
                }
                $form = $this->createForm(TrnVolunterCircleEventDetailsType::class, $trnVolunterCircleEventDetails);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $trnVolunterCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $user_id = $this->getUser()->getId();
                    $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);
                    $trnVolunterCircleEventDetails->setAppUser($appUser);
                    $trnVolunterCircleEventDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnVolunterCircleEventDetails->setCreatedOn(new \DateTime());
                    foreach ($trnVolunterCircleEventDetails->getTrnVolunterCircleEventSubEvents() as
                             $trnVolunterCircleEventSubEvents){
                        $trnVolunterCircleEventSubEvents->setAppUser($appUser);
                        $trnVolunterCircleEventSubEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnVolunterCircleEventSubEvents->setCreatedOn(new \DateTime());
                        $trnVolunterCircleEventSubEvents->setIsActive(true);
                    }
                    foreach ($trnVolunterCircleEventDetails->getTrnCircleEventRecurringDetails() as
                             $trnCircleEventRecurringDetails){
                        $trnCircleEventRecurringDetails->setAppUser($appUser);
                        $trnCircleEventRecurringDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnCircleEventRecurringDetails->setCreatedOn(new \DateTime());
                        $trnCircleEventRecurringDetails->setIsActive(true);
                        $trnCircleEventRecurringDetails->setTrnCircleEvents($trnCircleEvents);
                    }
                    foreach ($trnVolunterCircleEventDetails->getTrnVolunterCircleEventOnSiteAddresses() as
                             $trnVolunterCircleEventOnSiteAddresses){
                        $trnVolunterCircleEventOnSiteAddresses->setAppUser($appUser);
                        $trnVolunterCircleEventOnSiteAddresses->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnVolunterCircleEventOnSiteAddresses->setCreatedOn(new \DateTime());
                        $trnVolunterCircleEventOnSiteAddresses->setIsActive(true);
                    }
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($trnVolunterCircleEventDetails);
                    $entityManager->flush();
                    if( $nCurrentIndex == count($arrEventProductType) - 1 )
                        return $this->redirectToRoute('product_event_index');
                    else{
                        $nCurrentIndex = $nCurrentIndex + 1;
                        return $this->redirectToRoute('product_event_product_type', array('id' =>
                            $trnCircleEvents->getId(), 'currentIndex' => $nCurrentIndex));
                    }

                }
                return $this->render('transaction/circle_event/volunteer_in_time_form.html.twig', [
                    'circle_event' => $trnCircleEvents,
                    'form' => $form->createView(),
                    'label_title' => 'label.volunteer_in_time',
                    'label_button' => $strLabel,
                    'mode' => 'edit'
                ]);
            }
            case 'materialinkind' :{
                $trnMaterialInKindCircleEventDetailsArray = $trnCircleEvents->getTrnMaterialInKindCircleEventDetails();
                $strLabel = 'label.update';
                if(!empty($trnMaterialInKindCircleEventDetailsArray) && !empty($trnMaterialInKindCircleEventDetailsArray[0])){
                    $trnMaterialInKindCircleEventDetails = $trnMaterialInKindCircleEventDetailsArray[0];
                }
                else{
                    $trnMaterialInKindCircleEventDetails =  new TrnMaterialInKindCircleEventDetails();
                    $trnMaterialInKindCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $strLabel = 'label.create';
                }
                $form = $this->createForm(TrnMaterialInKindCircleEventDetailsType::class, $trnMaterialInKindCircleEventDetails);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $trnMaterialInKindCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $user_id = $this->getUser()->getId();
                    $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);
                    $trnMaterialInKindCircleEventDetails->setAppUser($appUser);
                    $trnMaterialInKindCircleEventDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnMaterialInKindCircleEventDetails->setCreatedOn(new \DateTime());

                    foreach ($trnMaterialInKindCircleEventDetails->getTrnMaterialInKindCircleEventSubEvents() as
                             $trnMaterialInKindCircleEventSubEvents){
                        $trnMaterialInKindCircleEventSubEvents->setAppUser($appUser);
                        $trnMaterialInKindCircleEventSubEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnMaterialInKindCircleEventSubEvents->setCreatedOn(new \DateTime());
                        $trnMaterialInKindCircleEventSubEvents->setIsActive(true);
                    }

                    foreach ($trnMaterialInKindCircleEventDetails->getTrnCircleEventRecurringDetails() as
                             $trnCircleEventRecurringDetails){
                        $trnCircleEventRecurringDetails->setAppUser($appUser);
                        $trnCircleEventRecurringDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnCircleEventRecurringDetails->setCreatedOn(new \DateTime());
                        $trnCircleEventRecurringDetails->setIsActive(true);
                        $trnCircleEventRecurringDetails->setTrnCircleEvents($trnCircleEvents);
                    }
                    foreach ($trnMaterialInKindCircleEventDetails->getTrnMaterialInKindCircleEventCollectionCentres() as
                    $trnMaterialInKindCircleEventCollectionCentre){
                        $trnMaterialInKindCircleEventCollectionCentre->setAppUser($appUser);
                        $trnMaterialInKindCircleEventCollectionCentre->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnMaterialInKindCircleEventCollectionCentre->setCreatedOn(new \DateTime());
                        $trnMaterialInKindCircleEventCollectionCentre->setIsActive(true);
                    }

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($trnMaterialInKindCircleEventDetails);
                    $entityManager->flush();

                    if( $nCurrentIndex == count($arrEventProductType) - 1 )
                        return $this->redirectToRoute('product_event_index');
                    else{
                        $nCurrentIndex = $nCurrentIndex + 1;
                        return $this->redirectToRoute('product_event_product_type', array('id' =>
                            $trnCircleEvents->getId(), 'currentIndex' => $nCurrentIndex));
                    }
                }
                return $this->render('transaction/circle_event/material_in_kind_form.html.twig', [
                    'circle_event' => $trnCircleEvents,
                    'form' => $form->createView(),
                    'label_title' => 'label.material_in_kind',
                    'label_button' => $strLabel,
                    'mode' => 'edit'
                ]);
                break;
            }
            case 'fundraiser':{
                $trnFundRaiserCircleEventDetailsArray = $trnCircleEvents->getTrnFundRaiserCircleEventDetails();
                $strLabel = 'label.update';
                if (!empty($trnFundRaiserCircleEventDetailsArray) && !empty($trnFundRaiserCircleEventDetailsArray[0])) {
                    $trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetailsArray[0];
                }
                else {
                    $trnFundRaiserCircleEventDetails =  new TrnFundRaiserCircleEventDetails();
                    $trnFundRaiserCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $strLabel = 'label.create';
                }
                $form = $this->createForm(TrnFundRaiserCircleEventDetailsType::class, $trnFundRaiserCircleEventDetails);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $trnFundRaiserCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $user_id = $this->getUser()->getId();
                    $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);
                    $trnFundRaiserCircleEventDetails->setAppUser($appUser);
                    $trnFundRaiserCircleEventDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnFundRaiserCircleEventDetails->setCreatedOn(new \DateTime());

                    foreach ($trnFundRaiserCircleEventDetails->getTrnFundRaiserCircleEventSubEvents() as
                             $trnFundRaiserCircleEventSubEvents){
                        $trnFundRaiserCircleEventSubEvents->setAppUser($appUser);
                        $trnFundRaiserCircleEventSubEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnFundRaiserCircleEventSubEvents->setCreatedOn(new \DateTime());
                        $trnFundRaiserCircleEventSubEvents->setIsActive(true);
                    }

                    foreach ($trnFundRaiserCircleEventDetails->getTrnCircleEventRecurringDetails() as
                             $trnCircleEventRecurringDetails){
                        $trnCircleEventRecurringDetails->setAppUser($appUser);
                        $trnCircleEventRecurringDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnCircleEventRecurringDetails->setCreatedOn(new \DateTime());
                        $trnCircleEventRecurringDetails->setIsActive(true);
                        $trnCircleEventRecurringDetails->setTrnCircleEvents($trnCircleEvents);
                    }

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($trnFundRaiserCircleEventDetails);
                    $entityManager->flush();

                    if( $nCurrentIndex == count($arrEventProductType) - 1 )
                        return $this->redirectToRoute('product_event_index');
                    else{
                        $nCurrentIndex = $nCurrentIndex + 1;
                        return $this->redirectToRoute('product_event_product_type', array('id' =>
                            $trnCircleEvents->getId(), 'currentIndex' => $nCurrentIndex));
                    }
                }
                return $this->render('transaction/circle_event/fund_raiser.html.twig', [
                    'circle_event' => $trnCircleEvents,
                    'form' => $form->createView(),
                    'label_title' => 'label.fund_raiser',
                    'label_button' => $strLabel,
                    'mode' => 'edit'
                ]);
                break;
            }
        }
    }
    /**
     * @Route("/upload/{id}", name="upload", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     */
    public  function upload(Request $request, TrnCircleEvents  $trnCircleEvents, FileUploaderHelper $fileUploaderHelper): Response
    {
        $form = $this->createForm(TrnCircleEventUploadType::class, $trnCircleEvents);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $content = $form->get('trnCircleEventUploadedDocuments');
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($trnCircleEvents->getTrnCircleEventUploadedDocuments() as $key => $trnCircleEventUploadedDocuments) {
                $mediaType = strtolower($content[$key]['mstUploadDocumentType']->getData()->getUploadDocumentType());
                if ($mediaType == 'image' ) {
                    $imageContentFile = $content[$key]['uploadedFilePath']->getData();
                    $newFilename = $fileUploaderHelper->uploadPublicFile($imageContentFile,
                        $content[0]['mediaName']->getData().uniqid(),null);
                    $trnCircleEventUploadedDocuments->setUploadedFilePath($newFilename);
                }
                $trnCircleEventUploadedDocuments->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                $trnCircleEventUploadedDocuments->setCreatedOn(new \DateTime());
                $trnCircleEventUploadedDocuments->setIsActive(true);
                $trnCircleEventUploadedDocuments->setlocationLatitude($trnCircleEvents->getLocationLatitude());
                $trnCircleEventUploadedDocuments->setLocationLongitude($trnCircleEvents->getLocationLongitude());
            }
            $entityManager->persist($trnCircleEvents);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('product_event_index');
        }
        return $this->render('transaction/circle_event/upload.html.twig', [
            'circle' => $trnCircleEvents,
            'form' => $form->createView(),
            'label_title' => 'label.circle_event',
            'label_button' => 'label.update',
            'path_index' => 'product_event_index',
            'path_edit' => 'product_event_edit',
            'path_delete' => 'product_event_delete',
        ]);
    }

    /**
     * @Route("/distribute_event/{id}", name="distribute_event", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function distributeEvent(Request $request, TrnCircleEvents  $trnCircleEvents): Response
    {
        $newCircleEvents = new TrnCircleEvents();
        $reflectionClass = new \ReflectionClass($trnCircleEvents);
        $methods = array_filter($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC), function(\ReflectionMethod $method){
            return strpos($method->name, "get") === 0;
        });
        foreach ($methods as $method) {
            $methodName = $method->name;
            //replace the 'get' string of method with 'set'
            $setter = str_replace("get", "set", $methodName);
            $value = $trnCircleEvents->$methodName();
            $bIsContinue = false;
            switch (strtolower($setter)) {
                case 'settrncircleeventrequesttoparticipates':
                case 'setid':
                case 'settrnvoluntercircleeventdetails':
                case 'settrnmaterialinkindcircleeventdetails':
                case 'settrnmaterialreceivedatcollectioncentres':
                case 'settrnvoluntercircleeventvolunterdetails':
                case 'settrncircleeventbroadcastdetails':
                case 'settrncircleeventgoodnesstimelines':
                case 'setaddress':
                case 'setgeocode':
                case 'setisdistributedevent':
                case 'setchildtrncircleevents':
                case 'settrnfundraisercircleeventsubevents':
                case 'settrncircleeventrecurringdetails':
                    {
                    $bIsContinue = true;
                    break;
                }
                case 'settrncircleeventuploadeddocuments': {
                    foreach ($value as $trnCircleEventUploadedDocument){
                        $newTrnCircleEventUploadedDocument = clone $trnCircleEventUploadedDocument;
                        $newCircleEvents->addTrnCircleEventUploadedDocument($newTrnCircleEventUploadedDocument);
                    }
                    $bIsContinue = true;
                    break;
                }
                case 'settrncircleeventinvitations': {
                    foreach ($value as $trnCircleEventInvitation){
                        $newTrnCircleEventInvitation = clone $trnCircleEventInvitation;
                        $newCircleEvents->addTrnCircleEventInvitation($newTrnCircleEventInvitation);
                    }
                    $bIsContinue = true;
                    break;
                }
                case 'settrnfundraisercircleeventdetails': {
                    foreach ($value as $trnFundRaiserCircleEventDetails){
                        $newTrnFundRaiserCircleEventDetails = clone $trnFundRaiserCircleEventDetails;
                        foreach ($newTrnFundRaiserCircleEventDetails->getTrnCircleEventRecurringDetails() as $trnCircleEventRecurringDetails) {
                            $newTrnFundRaiserCircleEventDetails->removeTrnCircleEventRecurringDetail($trnCircleEventRecurringDetails);
                            $newTrnFundRaiserCircleEventDetails->addTrnCircleEventRecurringDetail(clone $trnCircleEventRecurringDetails);
                        }
                        $newTrnFundRaiserCircleEventDetails->setTrnFundRaiserCircleEventSubEvents();
                        $newCircleEvents->addTrnFundRaiserCircleEventDetail($newTrnFundRaiserCircleEventDetails);
                    }
                    $bIsContinue = true;
                    break;
                }
                case 'setmsteventproducttype': {
                    foreach ($value as $mstEventProductType){
                        if( $mstEventProductType->getEventProductType() == 'Fundraiser'){
                            $newCircleEvents->addMstEventProductType($mstEventProductType);
                        }
                    }
                    $bIsContinue = true;
                    break;
                }
            }
            if ($bIsContinue)
                continue;
            $newCircleEvents->$setter($value);
        }
        $trnFundRaiserCircleEventDetails = null;
        $trnFundRaiserCircleEventDetailsArray = $newCircleEvents->getTrnFundRaiserCircleEventDetails();
        if (!empty($trnFundRaiserCircleEventDetailsArray) && !empty($trnFundRaiserCircleEventDetailsArray[0])) {
            $trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetailsArray[0];
        }
        $form = $this->createForm(TrnCircleEventDistributeType::class, $trnFundRaiserCircleEventDetails);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newCircleEvents->setParentTrnCircleEvents($trnCircleEvents);
            $trnFundRaiserCircleEventDetails->setIsDistributedEvent(false);
            $trnFundRaiserCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
            $user_id = $this->getUser()->getId();
            $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);
            $trnFundRaiserCircleEventDetails->setAppUser($appUser);
            $trnFundRaiserCircleEventDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $trnFundRaiserCircleEventDetails->setCreatedOn(new \DateTime());
            $user_id = $this->getUser()->getId();
            $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($trnFundRaiserCircleEventDetails->getTrnFundRaiserCircleEventSubEvents() as
                     $trnFundRaiserCircleEventSubEvents){
                $trnFundRaiserCircleEventSubEvents->setAppUser($appUser);
                $trnFundRaiserCircleEventSubEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $trnFundRaiserCircleEventSubEvents->setCreatedOn(new \DateTime());
                $trnFundRaiserCircleEventSubEvents->setIsActive(true);
                $entityManager->persist($trnFundRaiserCircleEventSubEvents);
            }
            foreach ($trnFundRaiserCircleEventDetails->getTrnCircleEventRecurringDetails() as
                     $trnCircleEventRecurringDetails){
                $trnCircleEventRecurringDetails->setAppUser($appUser);
                $trnCircleEventRecurringDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $trnCircleEventRecurringDetails->setCreatedOn(new \DateTime());
                $trnCircleEventRecurringDetails->setIsActive(true);
                $trnCircleEventRecurringDetails->setTrnCircleEvents($trnCircleEvents);
                $entityManager->persist($trnCircleEventRecurringDetails);
            }
            $entityManager->persist($trnFundRaiserCircleEventDetails);
            $trnFundRaiserCircleEventDetails->setTrnCircleEvents($newCircleEvents);
            $entityManager->persist($newCircleEvents);
            $this->getDoctrine()->getManager()->flush();
            $newCircleEvents->addTrnFundRaiserCircleEventDetail($trnFundRaiserCircleEventDetails);
            $entityManager->persist($newCircleEvents);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('product_event_index');
        }
        return $this->render('transaction/circle_event/distribute_event.html.twig', [
            'data' => $newCircleEvents,
            'form' => $form->createView(),
            'label_title' => 'label.circle_event',
            'label_button' => 'label.update',
            'path_index' => 'product_event_index',
            'path_edit' => 'product_event_edit',
            'path_delete' => 'product_event_delete'
        ]);
    }
}