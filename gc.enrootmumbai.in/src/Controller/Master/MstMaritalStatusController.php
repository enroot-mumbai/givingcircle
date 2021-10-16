<?php

namespace App\Controller\Master;

use App\Entity\Master\MstMaritalStatus;
use App\Form\Master\MstMaritalStatusType;
use App\Repository\Master\MstMaritalStatusRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/marital_status" , name="master_marital_status_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstMaritalStatusController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstMaritalStatusRepository $mstMaritalStatusRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstMaritalStatusRepository $mstMaritalStatusRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstMaritalStatusRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_marital_status/index.html.twig', [
            'mst_marital_statuses' => $pagination,
            'path_index' => 'master_marital_status_index',
            'path_add' => 'master_marital_status_add',
            'path_edit' => 'master_marital_status_edit',
            'path_show' => 'master_marital_status_show',
            'label_title' => 'label.marital_status',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstMaritalStatusRepository $mstMaritalStatusRepository
     * @return Response
     */
    public function new(Request $request, MstMaritalStatusRepository $mstMaritalStatusRepository): Response
    {
        $mstMaritalStatus = new MstMaritalStatus();
        $form = $this->createForm(MstMaritalStatusType::class, $mstMaritalStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstMaritalStatus->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstMaritalStatus);
            $entityManager->flush();

            return $this->redirectToRoute('master_marital_status_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_marital_status' => $mstMaritalStatus,
            'form' => $form->createView(),
            'index_path' => 'master_marital_status_index',
            'label_title' => 'label.marital_status',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstMaritalStatus $mstMaritalStatus
     * @return Response
     */
    public function edit(Request $request, MstMaritalStatus $mstMaritalStatus): Response
    {
        $form = $this->createForm(MstMaritalStatusType::class, $mstMaritalStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_marital_status_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_marital_status' => $mstMaritalStatus,
            'form' => $form->createView(),
            'index_path' => 'master_marital_status_index',
            'label_title' => 'label.marital_status',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstMaritalStatus $mstMaritalStatus
     * @return Response
     */
    public function delete(Request $request, MstMaritalStatus $mstMaritalStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstMaritalStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstMaritalStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_marital_status_index');
    }
}
