<?php

namespace App\Controller\SystemApp;
use App\Entity\SystemApp\AppModule;
use App\Form\SystemApp\AppSubModuleAccessType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\SystemApp\AppSubModule;
use App\Form\SystemApp\AppSubModuleType;
use App\Repository\SystemApp\AppSubModuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/core/system/submodule", name="system_submodule_")
 * @IsGranted("ROLE_SYS_ADMIN")
 */
class AppSubModuleController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param AppSubModuleRepository $appSubModuleRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(AppSubModuleRepository $appSubModuleRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $appSubModuleRepository->getSubModuleByModuleId($request->query->get('appmodule_id'));

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('system_app/app_sub_module/index.html.twig', [
            'app_submodules' => $pagination,
            'path_index' => 'system_submodule_index',
            'path_add' => 'system_submodule_add',
            'path_edit' => 'system_submodule_edit',
            'path_show' => 'system_submodule_show',
            'label_title' => 'label.submodule',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param AppSubModuleRepository $appSubModuleRepository
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, AppSubModuleRepository $appSubModuleRepository): Response
    {
        $appSubModule = new AppSubModule();
        $appModule = $this->getDoctrine()->getRepository(AppModule::class)->find($request->query->get('appmodule_id'));
        $sequenceNo = $appSubModuleRepository->findOneBySeqNo($request->query->get('appmodule_id'));
        $appSubModule->setSequenceNo(($sequenceNo[1] + 1));
        $appSubModule->setAppmodule($appModule);

        $form = $this->createForm(AppSubModuleType::class, $appSubModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uuidGenerator = Uuid::uuid4();
            $rowId = $uuidGenerator->toString();
            $appSubModule->setRowId($rowId);
            $entityManager = $this->getDoctrine()->getManager();
            if ($appSubModule->getParentId() == 'null' || $appSubModule->getParentId() == '') {

                $appSubModule->setParentId('0');

            }
            $entityManager->persist($appSubModule);
            $entityManager->flush();

            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('system_submodule_index', $request->query->all());
        }

        return $this->render('form/form.html.twig', [
            'app_submodule' => $appSubModule,
            'form' => $form->createView(),
            'index_path' => 'system_submodule_index',
            'label_title' => 'label.submodule',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param AppSubModule $appSubModule
     * @return Response
     */
    public function show(AppSubModule $appSubModule): Response
    {
        return $this->render('system_app/app_sub_module/show.html.twig', [
            'data' => $appSubModule,
            'path_index' => 'system_submodule_index',
            'path_edit' => 'system_submodule_edit',
            'path_delete' => 'system_submodule_delete',
            'label_button' => 'label.delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param AppSubModule $appSubModule
     * @return Response
     */
    public function edit(Request $request, AppSubModule $appSubModule): Response
    {
        $form = $this->createForm(AppSubModuleType::class, $appSubModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($appSubModule->getParentId() == 'null' || $appSubModule->getParentId() == '') {

                $appSubModule->setParentId(0);

            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('system_submodule_index', $request->query->all());
        }

        return $this->render('form/form.html.twig', [
            'app_submodule' => $appSubModule,
            'form' => $form->createView(),
            'index_path' => 'system_submodule_index',
            'label_title' => 'label.submodule',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/access/{id}", name="access", methods={"GET","POST"})
     * @param Request $request
     * @param AppSubModule $appSubModule
     * @return Response
     */
    public function access(Request $request, AppSubModule $appSubModule): Response
    {
        $form = $this->createForm(AppSubModuleAccessType::class, $appSubModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('system_submodule_index', $request->query->all());
        }

        return $this->render('form/form.html.twig', [
            'app_submodule' => $appSubModule,
            'form' => $form->createView(),
            'index_path' => 'system_submodule_index',
            'label_title' => 'label.submodule.access',
            'label_button' => 'label.update'
        ]);
    }


    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param AppSubModule $appSubModule
     * @return Response
     */
    public function delete(Request $request, AppSubModule $appSubModule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appSubModule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($appSubModule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('system_submodule_index');
    }
}
