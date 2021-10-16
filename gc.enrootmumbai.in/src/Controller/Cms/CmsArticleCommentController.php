<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsArticleComment;
use App\Form\Cms\CmsArticleCommentType;
use App\Repository\Cms\CmsArticleCommentRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/cms/article_comment", name="cms_article_comment_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsArticleCommentController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsArticleCommentRepository $cmsArticleCommentRepository
     * @param Request $request
     * @return Response
     */
    public function index(CmsArticleCommentRepository $cmsArticleCommentRepository, Request $request): Response
    {
        $article_id = $request->query->get('article_id');
        if(!$article_id) {
            return $this->redirectToRoute('cms_article_index');
        }
        $article = $this->getDoctrine()->getRepository(CmsArticle::class)->find($article_id);
        return $this->render('cms/cms_article_comment/index.html.twig', [
            'cms_article_comments' => $cmsArticleCommentRepository->findBy(['cmsArticle' => $article_id], ['commentDateTime' => 'DESC']),
            'article' => $article,
            'path_index' => 'cms_article_comment_index',
            'path_add' => 'cms_article_comment_add',
            'path_edit' => 'cms_article_comment_edit',
            'path_show' => 'cms_article_comment_show',
            'label_title' => 'label.comment',
            'label_heading' => 'label.blog'
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $article_id = $request->query->get('article_id');
        if(!$article_id) {
            return $this->redirectToRoute('cms_article_index');
        }
        $cmsArticle = $this->getDoctrine()->getRepository(CmsArticle::class)->find($article_id);
        $cmsArticleComment = new CmsArticleComment();
        $cmsArticleComment->setCmsArticle($cmsArticle);
        $form = $this->createForm(CmsArticleCommentType::class, $cmsArticleComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $cmsArticleComment->setCommentDateTime(new DateTime('now'));
            $entityManager->persist($cmsArticleComment);
            $entityManager->flush();

            return $this->redirectToRoute('cms_article_comment_index', $request->query->all());
        }

        return $this->render('cms/cms_article_comment/form.html.twig', [
            'cms_article_comment' => $cmsArticleComment,
            'article' => $cmsArticle,
            'form' => $form->createView(),
            'index_path' => 'cms_article_comment_index',
            'label_title' => 'label.comment',
            'label_button' => 'label.create',
            'label_heading' => 'label.blog',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsArticleComment $cmsArticleComment
     * @return Response
     */
    public function edit(Request $request, CmsArticleComment $cmsArticleComment): Response
    {
        $article_id = $request->query->get('article_id');
        if(!$article_id) {
            return $this->redirectToRoute('cms_article_index');
        }
        $cmsArticle = $this->getDoctrine()->getRepository(CmsArticle::class)->find($article_id);
        $form = $this->createForm(CmsArticleCommentType::class, $cmsArticleComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_article_comment_index', $request->query->all());
        }

        return $this->render('cms/cms_article_comment/form.html.twig', [
            'cms_article_comment' => $cmsArticleComment,
            'form' => $form->createView(),
            'article' => $cmsArticle,
            'index_path' => 'cms_article_comment_index',
            'label_title' => 'label.comment',
            'label_button' => 'label.update',
            'label_heading' => 'label.blog',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/status", name="status", methods={"GET","POST"})
     * @param Request $request
     * @param CmsArticleCommentRepository $cmsArticleCommentRepository
     * @return Response
     */
    public function status(Request $request, CmsArticleCommentRepository $cmsArticleCommentRepository): Response
    {
        $comment_id = trim($request->request->get('comment_id'));
        $status = trim($request->request->get('status'));
        $response = $cmsArticleCommentRepository->updateStatus($comment_id, $status);
        $comment = $this->getDoctrine()->getRepository(CmsArticleComment::class)->find($comment_id);
        return $this->render('cms/cms_article_comment/_ajax_status.html.twig', [
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
        if ($this->isCsrfTokenValid('delete'.$cmsArticleComment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsArticleComment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_article_comment_index', $request->query->all());
    }
}
