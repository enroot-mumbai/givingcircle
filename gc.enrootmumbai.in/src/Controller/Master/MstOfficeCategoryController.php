<?php

namespace App\Controller\Master;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Master\MstOfficeCategory;
use App\Form\Master\MstOfficeCategoryType;
use App\Repository\Master\MstOfficeCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @Route("/core/master/general/office_category", name="master_office_category_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstOfficeCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstOfficeCategoryRepository $mstOfficeCategoryRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstOfficeCategoryRepository $mstOfficeCategoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstOfficeCategoryRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('master/mst_office_category/index.html.twig', [
            'mst_office_categories' => $pagination,
            'path_index' => 'master_office_category_index',
            'path_add' => 'master_office_category_add',
            'path_edit' => 'master_office_category_edit',
            'path_show' => 'master_office_category_show',
            'label_title' => 'label.office_category',
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
        $mstOfficeCategory = new MstOfficeCategory();
        $form = $this->createForm(MstOfficeCategoryType::class, $mstOfficeCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mstOfficeCategory->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstOfficeCategory);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_office_category_index');
        }
        return $this->render('form/form.html.twig', [
            'master_office_category' => $mstOfficeCategory,
            'form' => $form->createView(),
            'index_path' => 'master_office_category_index',
            'label_title' => 'label.office_category',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstOfficeCategory $mstOfficeCategory
     * @return Response
     */
    public function edit(Request $request, MstOfficeCategory $mstOfficeCategory): Response
    {
        $form = $this->createForm(MstOfficeCategoryType::class, $mstOfficeCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_office_category_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_office_category' => $mstOfficeCategory,
            'form' => $form->createView(),
            'index_path' => 'master_office_category_index',
            'label_title' => 'label.office_category',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstOfficeCategory $mstOfficeCategory
     * @return Response
     */
    public function delete(Request $request, MstOfficeCategory $mstOfficeCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstOfficeCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstOfficeCategory);
            $entityManager->flush();
        }
        return $this->redirectToRoute('master_office_category_index');
    }
}
