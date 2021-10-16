<?php

namespace App\Controller\SystemApp;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\SystemApp\AppRole;
use App\Form\SystemApp\AppRoleType;
use App\Repository\SystemApp\AppRoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/core/system/role", name="system_role_")
 * @IsGranted("ROLE_SYS_ADMIN")
 */
class AppRoleController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param AppRoleRepository $appRoleRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(AppRoleRepository $appRoleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $appRoleRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('system_app/app_role/index.html.twig', [
            'app_roles' => $pagination,
            'path_index' => 'system_role_index',
            'path_add' => 'system_role_add',
            'path_edit' => 'system_role_edit',
            'path_show' => 'system_role_show',
            'label_title' => 'label.role',
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
        $appRole = new AppRole();
        $form = $this->createForm(AppRoleType::class, $appRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uuidGenerator = Uuid::uuid4();
            $rowId = $uuidGenerator->toString();
            $appRole->setRowId($rowId);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appRole);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('system_role_index');
        }

        return $this->render('form/form.html.twig', [
            'app_role' => $appRole,
            'form' => $form->createView(),
            'label_title' => 'label.role',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(AppRole $appRole): Response
    {
        return $this->render('system_app/app_role/show.html.twig', [
            'data' => $appRole,
            'path_index' => 'system_role_index',
            'path_edit' => 'system_role_edit',
            'path_delete' => 'system_role_delete',
            'label_button' => 'label.delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param AppRole $appRole
     * @return Response
     */
    public function edit(Request $request, AppRole $appRole): Response
    {
        $form = $this->createForm(AppRoleType::class, $appRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('system_role_index');
        }

        return $this->render('form/form.html.twig', [
            'app_role' => $appRole,
            'form' => $form->createView(),
            'label_title' => 'label.role',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param AppRole $appRole
     * @return Response
     */
    public function delete(Request $request, AppRole $appRole): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appRole->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($appRole);
            $entityManager->flush();
        }
        return $this->redirectToRoute('system_role_index');
    }
}
