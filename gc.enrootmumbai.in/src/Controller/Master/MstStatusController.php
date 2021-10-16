<?php

namespace App\Controller\Master;

use App\Entity\Master\MstStatus;
use App\Form\Master\MstStatusType;
use App\Repository\Master\MstStatusRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/status" , name="master_status_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstStatusController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstStatusRepository $mstStatusRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstStatusRepository $mstStatusRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstStatusRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_status/index.html.twig', [
            'mst_statuses' => $pagination,
            'path_index' => 'master_status_index',
            'path_add' => 'master_status_add',
            'path_edit' => 'master_status_edit',
            'path_show' => 'master_status_show',
            'label_title' => 'label.status',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstStatusRepository $mstStatusRepository
     * @return Response
     */
    public function new(Request $request, MstStatusRepository $mstStatusRepository): Response
    {
        $mstStatus = new MstStatus();
        $form = $this->createForm(MstStatusType::class, $mstStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstStatus->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstStatus);
            $entityManager->flush();

            return $this->redirectToRoute('master_status_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_status' => $mstStatus,
            'form' => $form->createView(),
            'index_path' => 'master_status_index',
            'label_title' => 'label.status',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstStatus $mstStatus
     * @return Response
     */
    public function edit(Request $request, MstStatus $mstStatus): Response
    {
        $form = $this->createForm(MstStatusType::class, $mstStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_status_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_status' => $mstStatus,
            'form' => $form->createView(),
            'index_path' => 'master_status_index',
            'label_title' => 'label.status',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstStatus $mstStatus
     * @return Response
     */
    public function delete(Request $request, MstStatus $mstStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_status_index');
    }
}
