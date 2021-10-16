<?php

namespace App\Controller\Transaction;

use App\Entity\Master\MstNotificationStatus;
use App\Repository\Transaction\TrnNotificationsRepository;
use App\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Transaction\TrnNotifications;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @Route("/core/product/notifications", name="product_notifications_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class TrnNotificationsController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param TrnNotificationsRepository $trnNotificationsRepository
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function index(TrnNotificationsRepository $trnNotificationsRepository, TokenStorageInterface $tokenStorage): Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        return $this->render('transaction/notifications/index.html.twig', [
            'notifications' => $trnNotificationsRepository->findBy(array('isActive' => 1, 'appUser' => $appUser ), ['id' => 'DESC'] ),
            'path_index' => 'product_notifications_index',
            'label_title' => 'Notifications'
        ]);
    }

    /**
     * @Route("/update-notification-status", name="update-notification-status", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNotificationStatus(Request $request, NotificationService $notificationService) :JsonResponse{
        $notificationId = $request->get('notificationid');
        $notificationService->markNotificationAsRead($notificationId);
        return new JsonResponse('Successfully Updated the status');
    }
}