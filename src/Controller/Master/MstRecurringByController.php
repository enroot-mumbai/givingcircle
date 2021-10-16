<?php

namespace App\Controller\Master;

use App\Entity\Master\MstRecurringBy;
use App\Form\Master\MstRecurringByType;
use App\Repository\Master\MstRecurringByRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/recurring_by" , name="master_recurring_by_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstRecurringByController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstRecurringByRepository $mstRecurringByRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstRecurringByRepository $mstRecurringByRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstRecurringByRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_recurring_by/index.html.twig', [
            'mst_recurring_bys' => $pagination,
            'path_index' => 'master_recurring_by_index',
            'path_add' => 'master_recurring_by_add',
            'path_edit' => 'master_recurring_by_edit',
            'path_show' => 'master_recurring_by_show',
            'label_title' => 'label.recurring_by',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstRecurringByRepository $mstRecurringByRepository
     * @return Response
     */
    public function new(Request $request, MstRecurringByRepository $mstRecurringByRepository): Response
    {
        $mstRecurringBy = new MstRecurringBy();
        $form = $this->createForm(MstRecurringByType::class, $mstRecurringBy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstRecurringBy->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstRecurringBy);
            $entityManager->flush();

            return $this->redirectToRoute('master_recurring_by_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_recurring_by' => $mstRecurringBy,
            'form' => $form->createView(),
            'index_path' => 'master_recurring_by_index',
            'label_title' => 'label.recurring_by',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstRecurringBy $mstRecurringBy
     * @return Response
     */
    public function edit(Request $request, MstRecurringBy $mstRecurringBy): Response
    {
        $form = $this->createForm(MstRecurringByType::class, $mstRecurringBy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_recurring_by_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_recurring_by' => $mstRecurringBy,
            'form' => $form->createView(),
            'index_path' => 'master_recurring_by_index',
            'label_title' => 'label.recurring_by',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstRecurringBy $mstRecurringBy
     * @return Response
     */
    public function delete(Request $request, MstRecurringBy $mstRecurringBy): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstRecurringBy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstRecurringBy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_recurring_by_index');
    }
}
