<?php

namespace App\Controller\Master;

use App\Entity\Master\MstMediaSubCategory;
use App\Form\Master\MstMediaSubCategoryType;
use App\Repository\Master\MstMediaSubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/media_subcategory" , name="master_media_subcategory_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstMediaSubCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstMediaSubCategoryRepository $mstMediaSubCategoryRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstMediaSubCategoryRepository $mstMediaSubCategoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstMediaSubCategoryRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_media_subcategory/index.html.twig', [
            'mst_media_categories' => $pagination,
            'path_index' => 'master_media_subcategory_index',
            'path_add' => 'master_media_subcategory_add',
            'path_edit' => 'master_media_subcategory_edit',
            'path_show' => 'master_media_subcategory_show',
            'label_title' => 'label.subcategory',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstMediaSubCategoryRepository $mstMediaSubCategoryRepository
     * @return Response
     */
    public function new(Request $request, MstMediaSubCategoryRepository $mstMediaSubCategoryRepository): Response
    {
        $mstMediaSubCategory = new MstMediaSubCategory();
        $sequenceNo = $mstMediaSubCategoryRepository->findOneBySeqNo();
        $mstMediaSubCategory->setSequenceNo(($sequenceNo[1] + 1));
        $form = $this->createForm(MstMediaSubCategoryType::class, $mstMediaSubCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstMediaSubCategory->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstMediaSubCategory);
            $entityManager->flush();

            return $this->redirectToRoute('master_media_subcategory_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_media_subcategory' => $mstMediaSubCategory,
            'form' => $form->createView(),
            'index_path' => 'master_media_subcategory_index',
            'label_title' => 'label.subcategory',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstMediaSubCategory $mstMediaSubCategory
     * @return Response
     */
    public function edit(Request $request, MstMediaSubCategory $mstMediaSubCategory): Response
    {
        $form = $this->createForm(MstMediaSubCategoryType::class, $mstMediaSubCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_media_subcategory_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_media_subcategory' => $mstMediaSubCategory,
            'form' => $form->createView(),
            'index_path' => 'master_media_subcategory_index',
            'label_title' => 'label.subcategory',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/list", name="list", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function subCategoryList(Request $request): Response
    {
        $category_id = $request->query->get('q');
        $subCategoryList = $this->getDoctrine()->getRepository(MstMediaSubCategory::class)->getSubCategoryByCategoryId($category_id);
        return $this->json($subCategoryList);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstMediaSubCategory $mstMediaSubCategory
     * @return Response
     */
    public function delete(Request $request, MstMediaSubCategory $mstMediaSubCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstMediaSubCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstMediaSubCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_media_subcategory_index');
    }
}
