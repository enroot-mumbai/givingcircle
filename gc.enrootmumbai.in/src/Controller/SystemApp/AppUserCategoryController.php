<?php

namespace App\Controller\SystemApp;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\SystemApp\AppUserCategory;
use App\Form\SystemApp\AppUserCategoryType;
use App\Repository\SystemApp\AppUserCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/core/system/user_category", name="system_user_category_")
 * @IsGranted("ROLE_SYS_ADMIN")
 */
class AppUserCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param AppUserCategoryRepository $appUserTypeRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(AppUserCategoryRepository $appUserTypeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $appUserTypeRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('system_app/app_user_category/index.html.twig', [
            'app_user_categories' => $pagination,
            'path_index' => 'system_user_category_index',
            'path_add' => 'system_user_category_add',
            'path_edit' => 'system_user_category_edit',
            'path_show' => 'system_user_category_show',
            'label_title' => 'label.usercategory',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request): Response
    {
        $appUserType = new AppUserCategory();
        $form = $this->createForm(AppUserCategoryType::class, $appUserType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uuidGenerator = Uuid::uuid4();
            $rowId = $uuidGenerator->toString();
            $appUserType->setRowId($rowId);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appUserType);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('system_user_category_index');
        }

        return $this->render('form/form.html.twig', [
            'app_user_category' => $appUserType,
            'form' => $form->createView(),
            'index_path' => 'system_user_category_index',
            'label_title' => 'label.usertype',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param AppUserCategory $appUserType
     * @return Response
     */
    public function show(AppUserCategory $appUserType): Response
    {
        return $this->render('system_app/app_user_category/show.html.twig', [
            'data' => $appUserType,
            'path_index' => 'system_user_category_index',
            'path_edit' => 'system_user_category_edit',
            'path_delete' => 'system_user_category_delete',
            'label_button' => 'label.delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param AppUserCategory $appUserType
     * @return Response
     */
    public function edit(Request $request, AppUserCategory $appUserType): Response
    {
        $form = $this->createForm(AppUserCategoryType::class, $appUserType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('system_user_category_index');
        }

        return $this->render('form/form.html.twig', [
            'app_user_category' => $appUserType,
            'form' => $form->createView(),
            'label_title' => 'label.usercategory',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param AppUserCategory $appUserType
     * @return Response
     */
    public function delete(Request $request, AppUserCategory $appUserType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appUserType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($appUserType);
            $entityManager->flush();
        }
        return $this->redirectToRoute('system_user_category_index');
    }
}
