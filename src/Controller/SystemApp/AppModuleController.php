<?php

namespace App\Controller\SystemApp;


use App\Form\SystemApp\AppModuleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\SystemApp\AppModule;
use App\Form\SystemApp\AppModuleAccessType;
use App\Repository\SystemApp\AppModuleRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/core/system/module", name="system_module_")
 * @IsGranted("ROLE_SYS_ADMIN")
 */
class AppModuleController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param AppModuleRepository $appModuleRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(AppModuleRepository $appModuleRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $q = $request->query->get('q');
        $queryBuilder = $appModuleRepository->findAll();

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('system_app/app_module/index.html.twig', [
            'app_modules' => $pagination,
            'path_index' => 'system_module_index',
            'path_add' => 'system_module_add',
            'path_edit' => 'system_module_edit',
            'path_show' => 'system_module_show',
            'label_title' => 'label.module',
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

        $appModule = new AppModule();
        $form = $this->createForm(AppModuleType::class, $appModule);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uuidGenerator = Uuid::uuid4();
            $rowId = $uuidGenerator->toString();
            $appModule->setRowId($rowId);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appModule);
            $entityManager->flush();

            $this->addFlash('success', 'form.created_successfully');

            return $this->redirectToRoute('system_module_index');
        }
        return $this->render('form/form.html.twig', [
            'app_module' => $appModule,
            'form' => $form->createView(),
            'label_title' => 'label.module',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param AppModule $appModule
     * @return Response
     */
    public function show(AppModule $appModule): Response
    {
        return $this->render('system_app/app_module/show.html.twig', [
            'data' => $appModule,
            'path_index' => 'system_module_index',
            'path_edit' => 'system_module_edit',
            'path_delete' => 'system_module_delete',
            'label_button' => 'label.delete',


        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param AppModule $appModule
     * @return Response
     */
    public function edit(Request $request, AppModule $appModule): Response
    {
        $form = $this->createForm(AppModuleType::class, $appModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('system_module_index');
        }

        return $this->render('form/form.html.twig', [
            'app_module' => $appModule,
            'form' => $form->createView(),
            'index_path' => 'system_module_index',
            'label_title' => 'label.module',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/access/{id}", name="access", methods={"GET","POST"})
     * @param Request $request
     * @param AppModule $appModule
     * @return Response
     */
    public function access(Request $request, AppModule $appModule): Response
    {
        $form = $this->createForm(AppModuleAccessType::class, $appModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('system_module_index');
        }

        return $this->render('form/form.html.twig', [
            'app_module' => $appModule,
            'form' => $form->createView(),
            'index_path' => 'system_module_index',
            'label_title' => 'label.module.access',
            'label_button' => 'label.update'
        ]);
    }


    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param AppModule $appModule
     * @return Response
     */
    public function delete(Request $request, AppModule $appModule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appModule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($appModule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('system_module_index');
    }
}
