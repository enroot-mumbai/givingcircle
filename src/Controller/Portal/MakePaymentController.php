<?php

namespace App\Controller\Portal;

use App\Entity\Cms\CmsArticle;
use App\Entity\Master\MstStatus;
use App\Repository\Master\MstStatusRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCrowdFundEventOfflineTransferRepository;
use App\Service\MyAccountService;
use App\Service\NotificationService;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCrowdFundEvent;
use App\Entity\Transaction\TrnFundRaiserCircleEventSubEvents;
use App\Entity\Transaction\TrnOrder;
use App\Repository\Organization\OrgCompanyOfficeRepository;
use App\Service\Cart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use \Knp\Snappy\Pdf;

class MakePaymentController extends AbstractController
{
    /**
     * @Route("/portal/make-payment/payment", name="make-payment", methods={"POST"})
     * @param Request $request
     * @param Cart $cart
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     * @throws \Exception
     */
    public function payment(Request $request, Cart $cart, SessionInterface $session, TokenStorageInterface $tokenStorage): Response
    {
        $arrUserDetails = array();
        $arrUserDetails["fname-fund"]                            = $fname_fund 							=  $request->get("fname-fund");
        $arrUserDetails["lname-fund"]                            = $lname_fund 							=  $request->get("lname-fund");
        $arrUserDetails["email-id"]                              = $email_id 							=  $request->get("email-id");
        $arrUserDetails["mobile-no"]                             = $mobile_no  							=  $request->get("mobile-no");
        $arrUserDetails["panNum"]                                = $panNum 								=  $request->get("panNum");
        $arrUserDetails["txtNote"]                               = $txtNote 							=  $request->get("txtNote");
        $arrUserDetails["hdnTrnCircleEventsId"]                  = $hdnTrnCircleEventsId 				=  $request->get("hdnTrnCircleEventsId");
        $arrUserDetails["hdnTrnFundRaiserCircleEventSubEventId"] = $hdnTrnFundRaiserCircleEventSubEventId  =  $request->get("hdnTrnFundRaiserCircleEventSubEventId");
        $arrUserDetails["hdnTnCrowdFundEventId"]                 = $hdnTnCrowdFundEventId  =  $request->get("hdnTnCrowdFundEventId");
        $arrUserDetails["anonymous"]                             = $anonymous  =  $request->get("anonymous");
        $arrUserDetails["contributionAmount"]                    = $contributionAmount  =  $request->get("contributionAmount");
        $arrUserDetails["hdnChangeMakerArticleId"]               = $hdnChangeMakerArticleId 			=  $request->get("hdnChangeMakerArticleId");
        $appUser = null;
        $user = $tokenStorage->getToken()->getUser();
        if ( is_object($user) && get_class($user) == AppUser::class) {
            $appUser = $user;
        }
        $session->remove('expressDonate');
        $trnCircleEvent = null;
        $trnFundRaiserCircleEventSubEvent = null;
        $trnCrowdFundEvents = null;
        $changeMakerArticle = null;

        if(!empty($hdnChangeMakerArticleId) ) {
            $changeMakerArticle = $this->getDoctrine()->getRepository(CmsArticle::class)->find($hdnChangeMakerArticleId);
        } else {

            $trnCircleEvent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($hdnTrnCircleEventsId);

            if (!empty($hdnTrnFundRaiserCircleEventSubEventId)) {
                $trnFundRaiserCircleEventSubEvent = $this->getDoctrine()->getRepository(TrnFundRaiserCircleEventSubEvents::class)->find($hdnTrnFundRaiserCircleEventSubEventId);
            } elseif(!empty($hdnTnCrowdFundEventId)) {
                $trnCrowdFundEvents = $this->getDoctrine()->getRepository(TrnCrowdFundEvent::class)->find($hdnTnCrowdFundEventId);
            }
            if(empty($trnCircleEvent) && empty($trnCrowdFundEvents)) {
                //Todo Handle
                return $this->redirectToRoute('express-donate-listing');
            }
        }

        $cart->createCart($arrUserDetails, $trnCircleEvent, $trnFundRaiserCircleEventSubEvent, $trnCrowdFundEvents, $appUser, $changeMakerArticle);
        return $this->redirectToRoute('payu');
    }

    /**
     * @Route("/portal/make-payment/payment-thank-you", name="payment-thank-you", methods={"GET"})
     * @param Request $request
     * @param SessionInterface $session
     * @param NotificationService $notificationService
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function paymentThankYou(Request $request, SessionInterface $session, NotificationService
    $notificationService, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository,
                                    TrnCrowdFundEventOfflineTransferRepository
                                    $trnCrowdFundEventOfflineTransferRepository, MstStatusRepository $mstStatusRepository)
    {
        $trnOrderId = $session->get('trnOrderId');

        if (empty($trnOrderId))
            return $this->redirectToRoute('homepage');
        $trnOrder = $this->getDoctrine()->getRepository(TrnOrder::class)->find($trnOrderId);

        $objParentTrnCircleEvents = null;
        $mobileNo = $trnOrder->getUserMobileNo();
        $mobileNo = substr_replace($mobileNo, 'XXXX', -4);

        $targetAmountCheck = true;
        if(!empty($trnOrder->getChangeMakerArticle())) {

            $targetAmountCheck = false;

        } else {
            if(!empty($trnOrder->getTrnCircleEvent()->getParentTrnCircleEvents()))
                $objParentTrnCircleEvents = $trnOrder->getTrnCircleEvent()->getParentTrnCircleEvents();
            else
                $objParentTrnCircleEvents = $trnOrder->getTrnCircleEvent();
        }

        if($targetAmountCheck == true) {
            if ($objParentTrnCircleEvents->getIsCrowdFunding()) {
                $targetAmount = $objParentTrnCircleEvents->getTrnCrowdFundEvents()[0]->getTargetAmount();
            } else {
                // check if target amount achieved then mark the event as past event
                $targetAmount = $objParentTrnCircleEvents->getTrnFundRaiserCircleEventDetails()[0]->getTargetAmount();
            }
            $finalSum = 0;
            foreach ($objParentTrnCircleEvents->getTrnOrders() as $fundOrders) {
                $finalSum += $fundOrders->getTotalAmount();
            }

            $arrChildTrnCircleEvents = $objParentTrnCircleEvents->getChildTrnCircleEvents();
            foreach ($arrChildTrnCircleEvents as $childTrnCircleEvent) {
                foreach ($childTrnCircleEvent->getTrnOrders() as $fundOrders) {
                    $finalSum += $fundOrders->getTotalAmount();
                }
            }

            if ($finalSum >= $targetAmount) {
                $entityManager = $this->getDoctrine()->getManager();
                $objTrnCircleEvent = $trnOrder->getTrnCircleEvent();
                $objTrnCircleEvent->setIsTargetAchieved(1);
                $objTrnCircleEvent->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                => 'Expired']));
                $entityManager->persist($objTrnCircleEvent);
                $entityManager->flush();

                $arrChildTrnCircleEvents = $trnCircleEventsRepository->findBy(['parentTrnCircleEvents' => $objTrnCircleEvent]);
                foreach ($arrChildTrnCircleEvents as $childTrnCircleEvent) {
                    $childTrnCircleEvent->setMstStatus($objTrnCircleEvent->getMstStatus());
                    $entityManager->persist($childTrnCircleEvent);
                }

                //Event Fundraiser Creator Target Reached
                $notificationService->setAppUser($trnOrder->getTrnCircleEvent()->getAppUser());
                $notificationService->setTrnCircle($trnOrder->getTrnCircleEvent()->getTrnCircle());
                $notificationService->setTrnCircleEvents($trnOrder->getTrnCircleEvent());
                $notificationService->setDonorName($trnOrder->getUserFirstName() . ' ' . $trnOrder->getUserLastName());
                $notificationService->setDonationAmount($trnOrder->getTransactionAmount());
                $notificationService->doProcess('Event Fundraiser Participant Target Reached');
                //Event Fundraiser Participant Target Reached
                $donors = $myAccountService->getDonorList($trnOrder->getTrnCircleEvent());
                foreach ($donors as $userData) {
                    if (!empty($userData) && !empty($userData['appUser'])) {
                        $notificationService->setDonationAmount($userData['donationAmount']);
                        $notificationService->setAppUser($userData['appUser']);
                        $notificationService->doProcess('Event Fundraiser Participant Target Reached');
                    }
                }
            }

            if (!empty($trnOrder) && !empty($trnOrder->getAppUser())) {
                $notificationService->setAppUser($trnOrder->getAppUser());
                $notificationService->setTrnCircle($trnOrder->getTrnCircleEvent()->getTrnCircle());
                $notificationService->setTrnCircleEvents($trnOrder->getTrnCircleEvent());
                $notificationService->setDonationAmount($trnOrder->getTransactionAmount());
                $notificationService->doProcess('Fundriaser Donation');
            }
        } else {
            // notification to GC
        }
        return $this->render('portal/funds/payment-successful.html.twig', ['trnOrder' => $trnOrder, 'mobileNo' =>
            $mobileNo]);
    }

    /**
     * @Route("/portal/make-payment/print-eighty-g-certificate/{transaction}", name="print-eighty-g-certificate",
     *     methods={"GET"})
     * @param Request $request
     * @param $transaction
     * @param Pdf $pdf
     * @return PdfResponse
     */
    public function print80GCertificate(Request $request,$transaction, Pdf $pdf) :PdfResponse
    {
        $trnOrder = $this->getDoctrine()->getRepository(TrnOrder::class)->findOneBy(array('transactionId' =>
            $transaction));
        $trnFundRaiserCircleEventSubEvents = $trnOrder->getTrnFundRaiserCircleEventSubEvents();
        $trnCircleEvent = $trnOrder->getTrnCircleEvent();
        $appUserInfo = $trnCircleEvent->getTrnCircle()->getAppUser()->getAppUserInfo();
        $trnOrganizationDetails = $trnCircleEvent->getTrnCircle()->getAppUser()->getTrnOrganizationDetails()[0];
        $response  =  $this->renderView('portal/receipts/80g-certificate.html.twig', [ 'trnOrder' => $trnOrder,
            'trnFundRaiserCircleEventSubEvents' => $trnFundRaiserCircleEventSubEvents, 'trnCircleEvent' =>
                $trnCircleEvent, 'appUserInfo' => $appUserInfo, 'trnOrganizationDetails'=> $trnOrganizationDetails]);
        return new PdfResponse($pdf->getOutputFromHtml($response), $transaction.'_80g_certificate.pdf');
    }

    /**
     * @Route("/portal/make-payment/print-receipt/{transaction}", name="print-receipt",
     *     methods={"GET"})
     * @param Request $request
     * @param $transaction
     * @param Pdf $pdf
     * @param OrgCompanyOfficeRepository $orgCompanyOfficeRepository
     * @return PdfResponse
     */
    public function printReceipt(Request $request, $transaction, Pdf $pdf, OrgCompanyOfficeRepository
    $orgCompanyOfficeRepository) :PdfResponse
    {
        $orgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter
        ('company_id'));
        $objOrgCompanyOffice = $orgCompanyOfficeRepository->findOneBy(['orgCompany' => $this->getParameter
        ('company_id'), 'mstOfficeCategory' => 1]);
        $trnOrder = $this->getDoctrine()->getRepository(TrnOrder::class)->findOneBy(array('transactionId' =>
            $transaction));
        $response  =  $this->renderView('portal/receipts/print.html.twig', [ 'trnOrder' => $trnOrder, 'objOrgCompanyOffice' => $objOrgCompanyOffice]);
        return new PdfResponse($pdf->getOutputFromHtml($response), $transaction.'_receipt.pdf');
    }
}