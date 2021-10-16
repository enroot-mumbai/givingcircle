<?php

namespace App\Controller\Portal;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstState;
use App\Entity\Master\MstUserMemberType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnAppUserContacts;
use App\Entity\Transaction\TrnBankDetails;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnOrganizationUploadDocuments;
use App\Entity\Transaction\TrnVolunterAvailability;
use App\Entity\Transaction\TrnVolunterDetail;
use App\Entity\Transaction\TrnVolunteerDocument;
use App\Entity\Transaction\TrnVolunterInterest;
use App\Form\SystemApp\AppUserMyAccountAboutType;
use App\Form\SystemApp\AppUserOrganizationMyAccountAboutType;
use App\Form\Transaction\TrnVolunterCircleEventDetailsPortalType;
use App\Repository\Master\MstAreaInterestRepository;
use App\Repository\Master\MstEventProductTypeRepository;
use App\Repository\Master\MstSkillSetRepository;
use App\Repository\Master\MstSourceOfInformationRepository;
use App\Repository\Master\MstStatusRepository;
use App\Repository\Master\MstUploadDocumentTypeRepository;
use App\Repository\Transaction\TrnAppUserContactsRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnVolunterInterestRepository;
use App\Service\FileUploaderHelper;
use App\Service\Mailer;
use App\Service\MyAccountService;
use App\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MyNotificationController
 * @IsGranted("ROLE_APP_USER")
 */
class MyNotificationController extends AbstractController
{

    /**
     * @Route("/my-account/view-all-notifications", name="my-account-view-all-notifications", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param SessionInterface $session
     * @param NotificationService $notificationService
     * @param MstEventProductTypeRepository $mstEventProductTypeRepository
     * @return Response
     */
    public function viewAllNotifications(Request $request,  TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, SessionInterface $session, NotificationService $notificationService,
                                         MstEventProductTypeRepository  $mstEventProductTypeRepository)
    :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrLatestNotification = $notificationService->getLatestNotificationForUser($appUser);
        $arrMstEventProductType = $mstEventProductTypeRepository->findAll();
        return $this->render('portal/my-account/notifications/view-all-notifications.html.twig', [
            'arrLatestNotification' => $arrLatestNotification, 'arrMstEventProductType' => $arrMstEventProductType
        ]);
    }

    /**
     * @Route("/my-account/mark-notification-as-read", name="my-account-mark-notification-as-read", methods={"GET", "POST"})
     * @param Request $request
     * @param NotificationService $notificationService
     * @return JsonResponse
     */
    public function markNotificationAsRead(Request $request, NotificationService $notificationService):JsonResponse {
        $nNotificationId = $request->get('nNotificationId');
        $notificationService->markNotificationAsRead($nNotificationId);
        return new JsonResponse(array('Status' => 1, 'Message' => 'Successfully updated'));
    }

    /**
     * @Route("/my-account/ajax-view-all-notifications", name="my-account-ajax-view-all-notifications", methods={"POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param SessionInterface $session
     * @param NotificationService $notificationService
     * @return Response
     */
    public function _ajaxViewAllNotifications(Request $request,  TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, SessionInterface $session, NotificationService $notificationService)
    :Response {
        $arrParameters = array();
        $mstNotificationStatus =  $request->get('mstNotificationStatus');
        $quicksearch =  $request->get('quicksearch');
        if (!empty($mstNotificationStatus))
            $arrParameters['mstNotificationStatus'] = $mstNotificationStatus;
        if (!empty($quicksearch))
            $arrParameters['quicksearch'] = $quicksearch;

        $projects_event = $request->get('projects_event');
        if (!empty($projects_event))
            $arrParameters['projects_event'] = $projects_event;

        $mstEventProductType = $request->get('mstEventProductType');
        if (!empty($mstEventProductType))
            $arrParameters['mstEventProductType'] = $mstEventProductType;

        $from = $request->get('from');
        if (!empty($from))
            $arrParameters['from'] = $from;

        $to = $request->get('to');
        if (!empty($to))
            $arrParameters['to'] = $to;
        $appUser = $tokenStorage->getToken()->getUser();
        $arrLatestNotification = $notificationService->getLatestNotificationForUser($appUser, $arrParameters);
        //dd($arrLatestNotification);
        return $this->render('portal/my-account/notifications/_ajax-view-all-notifications.html.twig', [
            'arrLatestNotification' => $arrLatestNotification
        ]);
    }
}