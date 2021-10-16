<?php

namespace App\Controller\Transaction;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsArticleComment;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCrowdFundEventOfflineTransfer;
use App\Entity\Transaction\TrnOrder;
use App\Form\Transaction\TrnUserCommentsType;
use App\Repository\Transaction\TrnCircleEventCommentsRepository;
use App\Form\Cms\CmsArticleCommentType;
use App\Repository\Cms\CmsArticleCommentRepository;
use App\Service\EventService;
use App\Service\NotificationService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/product/event_payment", name="product_event_payment_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class TrnCircleEventPaymentController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param Request $request
     * @param EventService $eventService
     * @return Response
     */
    public function index(Request $request, EventService $eventService): Response
    {
        $event_id = $request->query->get('event_id');

        if(!$event_id) {
            return $this->redirectToRoute('product_event_index');
        }
        $trncircleevent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($event_id);

        $trnCircleEventPayments = $eventService->getAllEventPayments($trncircleevent);

        return $this->render('transaction/circle_event_payment/index.html.twig', [
            'trn_event_payments' => $trnCircleEventPayments,
            'trnCircleEvent' => $trncircleevent,
            'path_index' => 'product_event_payment_index',
            'label_title' => 'Payment',
            'label_heading' => 'label.circle_event'
        ]);
    }
}
