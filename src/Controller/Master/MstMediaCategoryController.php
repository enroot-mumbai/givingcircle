<?php

namespace App\Controller\Master;

use App\Entity\Master\MstMediaCategory;
use App\Form\Master\MstMediaCategoryType;
use App\Repository\Master\MstMediaCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/media_category" , name="master_media_category_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstMediaCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstMediaCategoryRepository $mstMediaCategoryRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstMediaCategoryRepository $mstMediaCategoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstMediaCategoryRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_media_category/index.html.twig', [
            'mst_media_categories' => $pagination,
            'path_index' => 'master_media_category_index',
            'path_add' => 'master_media_category_add',
            'path_edit' => 'master_media_category_edit',
            'path_show' => 'master_media_category_show',
            'label_title' => 'label.category',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstMediaCategoryRepository $mstMediaCategoryRepository
     * @return Response
     */
    public function new(Request $request, MstMediaCategoryRepository $mstMediaCategoryRepository): Response
    {
        $mstMediaCategory = new MstMediaCategory();
        $sequenceNo = $mstMediaCategoryRepository->findOneBySeqNo();
        $mstMediaCategory->setSequenceNo(($sequenceNo[1] + 1));
        $form = $this->createForm(MstMediaCategoryType::class, $mstMediaCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstMediaCategory->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstMediaCategory);
            $entityManager->flush();

            return $this->redirectToRoute('master_media_category_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_media_category' => $mstMediaCategory,
            'form' => $form->createView(),
            'index_path' => 'master_media_category_index',
            'label_title' => 'label.category',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstMediaCategory $mstMediaCategory
     * @return Response
     */
    public function edit(Request $request, MstMediaCategory $mstMediaCategory): Response
    {
        $form = $this->createForm(MstMediaCategoryType::class, $mstMediaCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_media_category_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_media_category' => $mstMediaCategory,
            'form' => $form->createView(),
            'index_path' => 'master_media_category_index',
            'label_title' => 'label.category',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstMediaCategory $mstMediaCategory
     * @return Response
     */
    public function delete(Request $request, MstMediaCategory $mstMediaCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstMediaCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstMediaCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_media_category_index');
    }
}
