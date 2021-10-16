<?php

namespace App\Controller\Master;

use App\Entity\Master\MstActivityTypes;
use App\Form\Master\MstActivityTypesType;
use App\Repository\Master\MstActivityTypesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/activity_types" , name="master_activity_types_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstActivityTypesController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstActivityTypesRepository $mstActivityTypesRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstActivityTypesRepository $mstActivityTypesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstActivityTypesRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_activity_types/index.html.twig', [
            'mst_activity_typess' => $pagination,
            'path_index' => 'master_activity_types_index',
            'path_add' => 'master_activity_types_add',
            'path_edit' => 'master_activity_types_edit',
            'path_show' => 'master_activity_types_show',
            'label_title' => 'label.activity_types',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstActivityTypesRepository $mstActivityTypesRepository
     * @return Response
     */
    public function new(Request $request, MstActivityTypesRepository $mstActivityTypesRepository): Response
    {
        $mstActivityTypes = new MstActivityTypes();
        $form = $this->createForm(MstActivityTypesType::class, $mstActivityTypes);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstActivityTypes->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstActivityTypes);
            $entityManager->flush();

            return $this->redirectToRoute('master_activity_types_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_activity_types' => $mstActivityTypes,
            'form' => $form->createView(),
            'index_path' => 'master_activity_types_index',
            'label_title' => 'label.activity_types',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstActivityTypes $mstActivityTypes
     * @return Response
     */
    public function edit(Request $request, MstActivityTypes $mstActivityTypes): Response
    {
        $form = $this->createForm(MstActivityTypesType::class, $mstActivityTypes);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_activity_types_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_activity_types' => $mstActivityTypes,
            'form' => $form->createView(),
            'index_path' => 'master_activity_types_index',
            'label_title' => 'label.activity_types',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstActivityTypes $mstActivityTypes
     * @return Response
     */
    public function delete(Request $request, MstActivityTypes $mstActivityTypes): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstActivityTypes->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstActivityTypes);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_activity_types_index');
    }
}
