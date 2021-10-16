<?php

namespace App\Controller\Master;

use App\Entity\Master\MstUserMemberType;
use App\Form\Master\MstUserMemberTypeType;
use App\Repository\Master\MstUserMemberTypeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/user_member_type" , name="master_user_member_type_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstUserMemberTypeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstUserMemberTypeRepository $mstUserMemberTypeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstUserMemberTypeRepository $mstUserMemberTypeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstUserMemberTypeRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_user_member_type/index.html.twig', [
            'mst_user_member_types' => $pagination,
            'path_index' => 'master_user_member_type_index',
            'path_add' => 'master_user_member_type_add',
            'path_edit' => 'master_user_member_type_edit',
            'path_show' => 'master_user_member_type_show',
            'label_title' => 'label.user_member_type',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstUserMemberTypeRepository $mstUserMemberTypeRepository
     * @return Response
     */
    public function new(Request $request, MstUserMemberTypeRepository $mstUserMemberTypeRepository): Response
    {
        $mstUserMemberType = new MstUserMemberType();
        $form = $this->createForm(MstUserMemberTypeType::class, $mstUserMemberType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstUserMemberType->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstUserMemberType);
            $entityManager->flush();

            return $this->redirectToRoute('master_user_member_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_user_member_type' => $mstUserMemberType,
            'form' => $form->createView(),
            'index_path' => 'master_user_member_type_index',
            'label_title' => 'label.user_member_type',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstUserMemberType $mstUserMemberType
     * @return Response
     */
    public function edit(Request $request, MstUserMemberType $mstUserMemberType): Response
    {
        $form = $this->createForm(MstUserMemberTypeType::class, $mstUserMemberType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_user_member_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_user_member_type' => $mstUserMemberType,
            'form' => $form->createView(),
            'index_path' => 'master_user_member_type_index',
            'label_title' => 'label.user_member_type',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstUserMemberType $mstUserMemberType
     * @return Response
     */
    public function delete(Request $request, MstUserMemberType $mstUserMemberType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstUserMemberType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstUserMemberType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_user_member_type_index');
    }
}
