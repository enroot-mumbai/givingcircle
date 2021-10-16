<?php

namespace App\Controller\Transaction;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsArticleComment;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEvents;
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
 * @Route("/core/product/event_comment", name="product_event_comment_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class TrnCircleEventCommentController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $event_id = $request->query->get('event_id');

        if(!$event_id) {
            return $this->redirectToRoute('product_event_index');
        }
        $trncircleevent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($event_id);

        $trnCircleEventComments = $this->getDoctrine()->getRepository(TrnCircleEventComments::class)->findBy([
            'trnCircleEvents' => $event_id], ['commentDateTime' => 'DESC']);

        return $this->render('transaction/circle_event_comment/index.html.twig', [
            'trn_event_comments' => $trnCircleEventComments,
            'event' => $trncircleevent,
            'path_index' => 'product_event_comment_index',
            'path_add' => 'product_event_comment_add',
            'path_edit' => 'product_event_comment_edit',
            'path_show' => 'product_event_comment_show',
            'label_title' => 'label.comment',
            'label_heading' => 'label.circle_event'
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $event_id = $request->query->get('event_id');

        if(!$event_id) {
            return $this->redirectToRoute('product_event_index');
        }
        $trncircleevent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($event_id);

        $eventComment = new TrnCircleEventComments();
        $eventComment->setTrnCircleEvents($trncircleevent);
        $form = $this->createForm(TrnUserCommentsType::class, $eventComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $eventComment->setCommentDateTime(new DateTime('now'));
            $entityManager->persist($eventComment);
            $entityManager->flush();

            return $this->redirectToRoute('product_event_comment_index', $request->query->all());
        }

        return $this->render('transaction/circle_event_comment/form.html.twig', [
            'cms_article_comment' => $eventComment,
            'event' => $trncircleevent,
            'form' => $form->createView(),
            'index_path' => 'product_event_comment_index',
            'label_title' => 'label.comment',
            'label_button' => 'label.create',
            'label_heading' => 'label.circle_event',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEventComments $eventComment
     * @param NotificationService $notificationService
     * @return Response
     */
    public function edit(Request $request, TrnCircleEventComments $eventComment, NotificationService $notificationService): Response
    {
        $event_id = $request->query->get('event_id');
        if(!$event_id) {
            return $this->redirectToRoute('product_event_index');
        }
        $trncircleevent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($event_id);
        $form = $this->createForm(TrnUserCommentsType::class, $eventComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $notificationService->setAppUser($eventComment->getAppUser());
            $notificationService->setTrnCircle($eventComment->getTrnCircleEvents()->getTrnCircle());
            $notificationService->setTrnCircleEvents($eventComment->getTrnCircleEvents());
            if($eventComment->getIsApproved()) {
                $notificationService->doProcess('Blog Comments Accepted');
            } else {
                $notificationService->doProcess('Blog Comments Rejected');
            }
            return $this->redirectToRoute('product_event_comment_index', $request->query->all());
        }

        return $this->render('transaction/circle_event_comment/form.html.twig', [
            'event_comment' => $eventComment,
            'form' => $form->createView(),
            'event' => $trncircleevent,
            'index_path' => 'product_event_comment_index',
            'label_title' => 'label.comment',
            'label_button' => 'label.update',
            'label_heading' => 'label.circle_event',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/status", name="status", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEventCommentsRepository $trnCircleEventCommentsRepository
     * @param NotificationService $notificationService
     * @return Response
     */
    public function status(Request $request, TrnCircleEventCommentsRepository $trnCircleEventCommentsRepository, NotificationService $notificationService): Response
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
        return $this->render('transaction/circle_event_comment/_ajax_status.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param CmsArticleComment $cmsArticleComment
     * @return Response
     */
    public function delete(Request $request, CmsArticleComment $cmsArticleComment): Response
    {
        /*if ($this->isCsrfTokenValid('delete'.$cmsArticleComment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsArticleComment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_event_comment_index', $request->query->all());*/
    }
}
