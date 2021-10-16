<?php

namespace App\Controller\Transaction;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsArticleComment;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnOrder;
use App\Form\Transaction\TrnUserCommentsType;
use App\Repository\Transaction\TrnCircleEventCommentsRepository;
use App\Form\Cms\CmsArticleCommentType;
use App\Repository\Cms\CmsArticleCommentRepository;
use App\Service\NotificationService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/product/event_donation", name="product_event_donation_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class TrnOrderController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $trnorders = $this->getDoctrine()->getRepository(TrnOrder::class)->findBy([], ['orderDateTime' => 'DESC']);

        return $this->render('transaction/order/index.html.twig', [
            'trn_orders' => $trnorders,
            'path_index' => 'product_event_donation_index',
            'path_show' => 'product_event_donation_show',
            'label_heading' => 'label.order'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param TrnOrder $trnOrder
     * @return Response
     */
    public function show(TrnOrder $trnOrder): Response
    {
        return $this->render('transaction/order/show.html.twig', [
            'data' => $trnOrder,
            'label_title' => 'label.order',
            'path_index' => 'product_event_index',
            'path_show' => 'product_event_show',
        ]);
    }

    /**
     * @Route("/status", name="status", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEventCommentsRepository $trnCircleEventCommentsRepository
     * @param NotificationService $notificationService
     * @return Response
     */
    /*public function status(Request $request, TrnCircleEventCommentsRepository $trnCircleEventCommentsRepository, NotificationService $notificationService): Response
    {
        $comment_id = trim($request->request->get('comment_id'));
        $status = trim($request->request->get('status'));

        $response = $trnCircleEventCommentsRepository->updateStatus($comment_id, $status);
        $comment = $this->getDoctrine()->getRepository(TrnCircleEventComments::class)->find($comment_id);
        $notificationService->setAppUser($comment->getAppUser());
        $notificationService->setTrnCircle($comment->getTrnCircle());
        $notificationService->setTrnCircleEvents($comment->getTrnCircleEvents());
        if ($status == 'approve') {
            $notificationService->doProcess('Blog Comments Accepted');
        } else {
            $notificationService->doProcess('Blog Comments Rejected');
        }
        return $this->render('transaction/order/_ajax_status.html.twig', [
            'comment' => $comment,
        ]);
    }*/
}
