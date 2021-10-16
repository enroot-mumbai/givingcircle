<?php

namespace App\Controller\Master;

use App\Entity\Master\MstJoinBy;
use App\Form\Master\MstJoinByType;
use App\Repository\Master\MstJoinByRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/join_by" , name="master_join_by_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstJoinByController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstJoinByRepository $mstJoinByRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstJoinByRepository $mstJoinByRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstJoinByRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_join_by/index.html.twig', [
            'mst_join_bys' => $pagination,
            'path_index' => 'master_join_by_index',
            'path_add' => 'master_join_by_add',
            'path_edit' => 'master_join_by_edit',
            'path_show' => 'master_join_by_show',
            'label_title' => 'label.join_by',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstJoinByRepository $mstJoinByRepository
     * @return Response
     */
    public function new(Request $request, MstJoinByRepository $mstJoinByRepository): Response
    {
        $mstJoinBy = new MstJoinBy();
        $form = $this->createForm(MstJoinByType::class, $mstJoinBy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstJoinBy->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstJoinBy);
            $entityManager->flush();

            return $this->redirectToRoute('master_join_by_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_join_by' => $mstJoinBy,
            'form' => $form->createView(),
            'index_path' => 'master_join_by_index',
            'label_title' => 'label.join_by',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstJoinBy $mstJoinBy
     * @return Response
     */
    public function edit(Request $request, MstJoinBy $mstJoinBy): Response
    {
        $form = $this->createForm(MstJoinByType::class, $mstJoinBy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_join_by_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_join_by' => $mstJoinBy,
            'form' => $form->createView(),
            'index_path' => 'master_join_by_index',
            'label_title' => 'label.join_by',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstJoinBy $mstJoinBy
     * @return Response
     */
    public function delete(Request $request, MstJoinBy $mstJoinBy): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstJoinBy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstJoinBy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_join_by_index');
    }
}
