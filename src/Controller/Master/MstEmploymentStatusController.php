<?php

namespace App\Controller\Master;

use App\Entity\Master\MstEmploymentStatus;
use App\Form\Master\MstEmploymentStatusType;
use App\Repository\Master\MstEmploymentStatusRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/employment_status" , name="master_employment_status_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstEmploymentStatusController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstEmploymentStatusRepository $mstEmploymentStatusRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstEmploymentStatusRepository $mstEmploymentStatusRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstEmploymentStatusRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_employment_status/index.html.twig', [
            'mst_employment_statuses' => $pagination,
            'path_index' => 'master_employment_status_index',
            'path_add' => 'master_employment_status_add',
            'path_edit' => 'master_employment_status_edit',
            'path_show' => 'master_employment_status_show',
            'label_title' => 'label.employment_status',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstEmploymentStatusRepository $mstEmploymentStatusRepository
     * @return Response
     */
    public function new(Request $request, MstEmploymentStatusRepository $mstEmploymentStatusRepository): Response
    {
        $mstEmploymentStatus = new MstEmploymentStatus();
        $form = $this->createForm(MstEmploymentStatusType::class, $mstEmploymentStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstEmploymentStatus->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstEmploymentStatus);
            $entityManager->flush();

            return $this->redirectToRoute('master_employment_status_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_employment_status' => $mstEmploymentStatus,
            'form' => $form->createView(),
            'index_path' => 'master_employment_status_index',
            'label_title' => 'label.employment_status',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstEmploymentStatus $mstEmploymentStatus
     * @return Response
     */
    public function edit(Request $request, MstEmploymentStatus $mstEmploymentStatus): Response
    {
        $form = $this->createForm(MstEmploymentStatusType::class, $mstEmploymentStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_employment_status_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_employment_status' => $mstEmploymentStatus,
            'form' => $form->createView(),
            'index_path' => 'master_employment_status_index',
            'label_title' => 'label.employment_status',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstEmploymentStatus $mstEmploymentStatus
     * @return Response
     */
    public function delete(Request $request, MstEmploymentStatus $mstEmploymentStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstEmploymentStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstEmploymentStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_employment_status_index');
    }
}
