<?php

namespace App\Controller\Master;

use App\Entity\Master\MstEventOccurrence;
use App\Form\Master\MstEventOccurrenceType;
use App\Repository\Master\MstEventOccurrenceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/event_occurrence" , name="master_event_occurrence_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstEventOccurrenceController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstEventOccurrenceRepository $mstEventOccurrenceRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstEventOccurrenceRepository $mstEventOccurrenceRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstEventOccurrenceRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_event_occurrence/index.html.twig', [
            'mst_event_occurrences' => $pagination,
            'path_index' => 'master_event_occurrence_index',
            'path_add' => 'master_event_occurrence_add',
            'path_edit' => 'master_event_occurrence_edit',
            'path_show' => 'master_event_occurrence_show',
            'label_title' => 'label.event_occurrence',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstEventOccurrenceRepository $mstEventOccurrenceRepository
     * @return Response
     */
    public function new(Request $request, MstEventOccurrenceRepository $mstEventOccurrenceRepository): Response
    {
        $mstEventOccurrence = new MstEventOccurrence();
        $form = $this->createForm(MstEventOccurrenceType::class, $mstEventOccurrence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstEventOccurrence->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstEventOccurrence);
            $entityManager->flush();

            return $this->redirectToRoute('master_event_occurrence_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_event_occurrence' => $mstEventOccurrence,
            'form' => $form->createView(),
            'index_path' => 'master_event_occurrence_index',
            'label_title' => 'label.event_occurrence',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstEventOccurrence $mstEventOccurrence
     * @return Response
     */
    public function edit(Request $request, MstEventOccurrence $mstEventOccurrence): Response
    {
        $form = $this->createForm(MstEventOccurrenceType::class, $mstEventOccurrence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_event_occurrence_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_event_occurrence' => $mstEventOccurrence,
            'form' => $form->createView(),
            'index_path' => 'master_event_occurrence_index',
            'label_title' => 'label.event_occurrence',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstEventOccurrence $mstEventOccurrence
     * @return Response
     */
    public function delete(Request $request, MstEventOccurrence $mstEventOccurrence): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstEventOccurrence->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstEventOccurrence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_event_occurrence_index');
    }
}
