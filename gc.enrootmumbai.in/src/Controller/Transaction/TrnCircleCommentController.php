<?php

namespace App\Controller\Transaction;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsArticleComment;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Form\Transaction\TrnUserCommentsType;
use App\Repository\Transaction\TrnCircleEventCommentsRepository;
use App\Form\Cms\CmsArticleCommentType;
use App\Repository\Cms\CmsArticleCommentRepository;
use App\Service\NotificationService;
use Container1lm0GPH\getTrnCircle2Service;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/product/circle_comment", name="product_circle_comment_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class TrnCircleCommentController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $circle_id = $request->query->get('circle_id');

        if(!$circle_id) {
            return $this->redirectToRoute('product_circle_index');
        }
        $trncircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circle_id);

        $trnCircleEventComments = $this->getDoctrine()->getRepository(TrnCircleEventComments::class)->findBy([
            'trnCircle' => $circle_id, 'trnCircleEvents' => null], ['commentDateTime' => 'DESC']);

        return $this->render('transaction/circle_comment/index.html.twig', [
            'trn_circle_comments' => $trnCircleEventComments,
            'circle' => $trncircle,
            'path_index' => 'product_circle_comment_index',
            'path_add' => 'product_circle_comment_add',
            'path_edit' => 'product_circle_comment_edit',
            'path_show' => 'product_circle_comment_show',
            'label_title' => 'label.comment',
            'label_heading' => 'label.circle'
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $circle_id = $request->query->get('circle_id');
        if(!$circle_id) {
            return $this->redirectToRoute('product_circle_index');
        }
        $trncircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circle_id);

        $circleComment = new TrnCircleEventComments();
        $circleComment->setTrnCircle($trncircle);
        $form = $this->createForm(TrnUserCommentsType::class, $circleComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $circleComment->setCommentDateTime(new DateTime('now'));
            $entityManager->persist($circleComment);
            $entityManager->flush();

            return $this->redirectToRoute('product_circle_comment_index', $request->query->all());
        }

        return $this->render('transaction/circle_comment/form.html.twig', [
            'cms_article_comment' => $circleComment,
            'circle' => $trncircle,
            'form' => $form->createView(),
            'index_path' => 'product_circle_comment_index',
            'label_title' => 'label.comment',
            'label_button' => 'label.create',
            'label_heading' => 'label.circle',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEventComments $circleComment
     * @param NotificationService $notificationService
     * @return Response
     */
    public function edit(Request $request, TrnCircleEventComments $circleComment, NotificationService $notificationService): Response
    {
        $circle_id = $request->query->get('circle_id');
        if(!$circle_id) {
            return $this->redirectToRoute('product_circle_index');
        }
        $circle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circle_id);
        $form = $this->createForm(TrnUserCommentsType::class, $circleComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $notificationService->setAppUser($circleComment->getAppUser());
            $notificationService->setTrnCircle($circleComment->getTrnCircle());
            if($circleComment->getIsApproved()) {
                $notificationService->doProcess('Blog Comments Accepted');
            } else {
                $notificationService->doProcess('Blog Comments Rejected');
            }

            return $this->redirectToRoute('product_circle_comment_index', $request->query->all());
        }

        return $this->render('transaction/circle_comment/form.html.twig', [
            'circle_comment' => $circleComment,
            'form' => $form->createView(),
            'circle' => $circle,
            'index_path' => 'product_circle_comment_index',
            'label_title' => 'label.comment',
            'label_button' => 'label.update',
            'label_heading' => 'label.circle',
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
    public function status(Request $request, TrnCircleEventCommentsRepository $trnCircleEventCommentsRepository,
                           NotificationService $notificationService): Response
    {
        $comment_id = trim($request->request->get('comment_id'));
        $status = trim($request->request->get('status'));

        $response = $trnCircleEventCommentsRepository->updateStatus($comment_id, $status);
        $comment = $this->getDoctrine()->getRepository(TrnCircleEventComments::class)->find($comment_id);
        $notificationService->setAppUser($comment->getAppUser());
        $notificationService->setTrnCircle($comment->getTrnCircle());
        if ($status == 'approve') {
            $notificationService->doProcess('Blog Comments Accepted');
        } else {
            $notificationService->doProcess('Blog Comments Rejected');
        }
        return $this->render('transaction/circle_comment/_ajax_status.html.twig', [
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

        return $this->redirectToRoute('product_circle_comment_index', $request->query->all());*/
    }
}
