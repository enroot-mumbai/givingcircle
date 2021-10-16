<?php

namespace App\Controller\Master;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Master\MstArticleCategory;
use App\Form\Master\MstArticleCategoryType;
use App\Repository\Master\MstArticleCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/core/master/general/article_category", name="master_article_category_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */

class MstArticleCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstArticleCategoryRepository $mstArticleCategoryRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(MstArticleCategoryRepository $mstArticleCategoryRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $mstArticleCategoryRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_article_category/index.html.twig', [
            'mst_article_categories' => $pagination,
            'path_index' => 'master_article_category_index',
            'path_add' => 'master_article_category_add',
            'path_edit' => 'master_article_category_edit',
            'path_show' => 'master_article_category_show',
            'label_title' => 'label.article_category',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $mstArticleCategory = new MstArticleCategory();
        $form = $this->createForm(MstArticleCategoryType::class, $mstArticleCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstArticleCategory->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstArticleCategory);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_article_category_index');
        }
        return $this->render('form/form.html.twig', [
            'mst_article_category' => $mstArticleCategory,
            'form' => $form->createView(),
            'index_path' => 'master_article_category_index',
            'label_title' => 'label.article_category',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstArticleCategory $mstArticleCategory
     * @return Response
     */
    public function edit(Request $request, MstArticleCategory $mstArticleCategory): Response
    {
        $form = $this->createForm(MstArticleCategoryType::class, $mstArticleCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_article_category_index');
        }
        return $this->render('form/form.html.twig', [
            'mst_article_category' => $mstArticleCategory,
            'form' => $form->createView(),
            'index_path' => 'master_article_category_index',
            'label_title' => 'label.article_category',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstArticleCategory $mstArticleCategory
     * @return Response
     */
    public function delete(Request $request, MstArticleCategory $mstArticleCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstArticleCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstArticleCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_article_category_index');
    }
}
