<?php

namespace App\Controller\Master;

use App\Entity\Master\MstUploadDocumentType;
use App\Form\Master\MstUploadDocumentTypeType;
use App\Repository\Master\MstUploadDocumentTypeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/upload_document_type" , name="master_upload_document_type_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstUploadDocumentTypeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstUploadDocumentTypeRepository $MstUploadDocumentTypeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstUploadDocumentTypeRepository $mstUploadDocumentTypeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstUploadDocumentTypeRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_upload_document_type/index.html.twig', [
            'mst_upload_document_types' => $pagination,
            'path_index' => 'master_upload_document_type_index',
            'path_add' => 'master_upload_document_type_add',
            'path_edit' => 'master_upload_document_type_edit',
            'path_show' => 'master_upload_document_type_show',
            'label_title' => 'label.upload_document_type',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstUploadDocumentTypeRepository $mstUploadDocumentTypeRepository
     * @return Response
     */
    public function new(Request $request, MstUploadDocumentTypeRepository $mstUploadDocumentTypeRepository): Response
    {
        $mstUploadDocumentType = new MstUploadDocumentType();
        $form = $this->createForm(MstUploadDocumentTypeType::class, $mstUploadDocumentType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mstUploadDocumentType->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstUploadDocumentType);
            $entityManager->flush();

            return $this->redirectToRoute('master_upload_document_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_upload_document_type' => $mstUploadDocumentType,
            'form' => $form->createView(),
            'index_path' => 'master_upload_document_type_index',
            'label_title' => 'label.upload_document_type',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstUploadDocumentType $mstUploadDocumentType
     * @return Response
     */
    public function edit(Request $request, MstUploadDocumentType $mstUploadDocumentType): Response
    {
        $form = $this->createForm(MstUploadDocumentTypeType::class, $mstUploadDocumentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_upload_document_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_upload_document_type' => $mstUploadDocumentType,
            'form' => $form->createView(),
            'index_path' => 'master_upload_document_type_index',
            'label_title' => 'label.upload_document_type',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstUploadDocumentType $mstUploadDocumentType
     * @return Response
     */
    public function delete(Request $request, MstUploadDocumentType $mstUploadDocumentType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstUploadDocumentType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstUploadDocumentType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_upload_document_type_index');
    }
}
