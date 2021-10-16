<?php

namespace App\Controller\Master;

use App\Entity\Master\MstUserQueryType;
use App\Form\Master\MstUserQueryTypeType;
use App\Repository\Master\MstUserQueryTypeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/user_query_type" , name="master_user_query_type_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstUserQueryTypeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstUserQueryTypeRepository $mstUserQueryTypeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstUserQueryTypeRepository $mstUserQueryTypeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstUserQueryTypeRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_user_query_type/index.html.twig', [
            'mst_user_query_types' => $pagination,
            'path_index' => 'master_user_query_type_index',
            'path_add' => 'master_user_query_type_add',
            'path_edit' => 'master_user_query_type_edit',
            'path_show' => 'master_user_query_type_show',
            'label_title' => 'label.user_query_type',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstUserQueryTypeRepository $mstUserQueryTypeRepository
     * @return Response
     */
    public function new(Request $request, MstUserQueryTypeRepository $mstUserQueryTypeRepository): Response
    {
        $mstUserQueryType = new MstUserQueryType();
        $form = $this->createForm(MstUserQueryTypeType::class, $mstUserQueryType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstUserQueryType->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstUserQueryType);
            $entityManager->flush();

            return $this->redirectToRoute('master_user_query_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_user_query_type' => $mstUserQueryType,
            'form' => $form->createView(),
            'index_path' => 'master_user_query_type_index',
            'label_title' => 'label.user_query_type',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstUserQueryType $mstUserQueryType
     * @return Response
     */
    public function edit(Request $request, MstUserQueryType $mstUserQueryType): Response
    {
        $form = $this->createForm(MstUserQueryTypeType::class, $mstUserQueryType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_user_query_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_user_query_type' => $mstUserQueryType,
            'form' => $form->createView(),
            'index_path' => 'master_user_query_type_index',
            'label_title' => 'label.user_query_type',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstUserQueryType $mstUserQueryType
     * @return Response
     */
    public function delete(Request $request, MstUserQueryType $mstUserQueryType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstUserQueryType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstUserQueryType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_user_query_type_index');
    }
}
