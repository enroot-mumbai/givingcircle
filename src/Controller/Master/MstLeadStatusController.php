<?php

namespace App\Controller\Master;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Master\MstLeadStatus;
use App\Form\Master\MstLeadStatusType;
use App\Repository\Master\MstLeadStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/core/master/general/lead_status", name="master_lead_status_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */

class MstLeadStatusController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstLeadStatusRepository $mstLeadStatusRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(MstLeadStatusRepository $mstLeadStatusRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $mstLeadStatusRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_lead_status/index.html.twig', [
            'mst_lead_statuses' => $pagination,
            'path_index' => 'master_lead_status_index',
            'path_add' => 'master_lead_status_add',
            'path_edit' => 'master_lead_status_edit',
            'path_show' => 'master_lead_status_show',
            'label_title' => 'label.lead_status',
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
        $mstLeadStatus = new MstLeadStatus();
        $form = $this->createForm(MstLeadStatusType::class, $mstLeadStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstLeadStatus->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstLeadStatus);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_lead_status_index');
        }
        return $this->render('form/form.html.twig', [
            'mst_lead_status' => $mstLeadStatus,
            'form' => $form->createView(),
            'index_path' => 'master_lead_status_index',
            'label_title' => 'label.lead_status',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstLeadStatus $mstLeadStatus
     * @return Response
     */
    public function edit(Request $request, MstLeadStatus $mstLeadStatus): Response
    {
        $form = $this->createForm(MstLeadStatusType::class, $mstLeadStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_lead_status_index');
        }
        return $this->render('form/form.html.twig', [
            'mst_lead_status' => $mstLeadStatus,
            'form' => $form->createView(),
            'index_path' => 'master_lead_status_index',
            'label_title' => 'label.lead_status',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstLeadStatus $mstLeadStatus
     * @return Response
     */
    public function delete(Request $request, MstLeadStatus $mstLeadStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstLeadStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstLeadStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_lead_status_index');
    }
}
