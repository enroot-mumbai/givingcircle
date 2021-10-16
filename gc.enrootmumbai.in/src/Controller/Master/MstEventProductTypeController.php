<?php

namespace App\Controller\Master;

use App\Entity\Master\MstEventProductType;
use App\Form\Master\MstEventProductTypeType;
use App\Repository\Master\MstEventProductTypeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/event_product_type" , name="master_event_product_type_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstEventProductTypeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstEventProductTypeRepository $mstEventProductTypeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstEventProductTypeRepository $mstEventProductTypeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstEventProductTypeRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_event_product_type/index.html.twig', [
            'mst_event_product_types' => $pagination,
            'path_index' => 'master_event_product_type_index',
            'path_add' => 'master_event_product_type_add',
            'path_edit' => 'master_event_product_type_edit',
            'path_show' => 'master_event_product_type_show',
            'label_title' => 'label.event_product_type',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstEventProductTypeRepository $mstEventProductTypeRepository
     * @return Response
     */
    public function new(Request $request, MstEventProductTypeRepository $mstEventProductTypeRepository): Response
    {
        $mstEventProductType = new MstEventProductType();
        $form = $this->createForm(MstEventProductTypeType::class, $mstEventProductType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstEventProductType->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstEventProductType);
            $entityManager->flush();

            return $this->redirectToRoute('master_event_product_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_event_product_type' => $mstEventProductType,
            'form' => $form->createView(),
            'index_path' => 'master_event_product_type_index',
            'label_title' => 'label.event_product_type',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstEventProductType $mstEventProductType
     * @return Response
     */
    public function edit(Request $request, MstEventProductType $mstEventProductType): Response
    {
        $form = $this->createForm(MstEventProductTypeType::class, $mstEventProductType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_event_product_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_event_product_type' => $mstEventProductType,
            'form' => $form->createView(),
            'index_path' => 'master_event_product_type_index',
            'label_title' => 'label.event_product_type',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstEventProductType $mstEventProductType
     * @return Response
     */
    public function delete(Request $request, MstEventProductType $mstEventProductType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstEventProductType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstEventProductType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_event_product_type_index');
    }
}
