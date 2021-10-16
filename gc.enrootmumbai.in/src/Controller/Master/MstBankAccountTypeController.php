<?php

namespace App\Controller\Master;

use App\Entity\Master\MstBankAccountType;
use App\Form\Master\MstBankAccountTypeType;
use App\Repository\Master\MstBankAccountTypeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/bank_account_type" , name="master_bank_account_type_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstBankAccountTypeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstBankAccountTypeRepository $mstBankAccountTypeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstBankAccountTypeRepository $mstBankAccountTypeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstBankAccountTypeRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_bank_account_type/index.html.twig', [
            'mst_bank_account_types' => $pagination,
            'path_index' => 'master_bank_account_type_index',
            'path_add' => 'master_bank_account_type_add',
            'path_edit' => 'master_bank_account_type_edit',
            'path_show' => 'master_bank_account_type_show',
            'label_title' => 'label.bank_account_type',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstBankAccountTypeRepository $mstBankAccountTypeRepository
     * @return Response
     */
    public function new(Request $request, MstBankAccountTypeRepository $mstBankAccountTypeRepository): Response
    {
        $mstBankAccountType = new MstBankAccountType();
        $form = $this->createForm(MstBankAccountTypeType::class, $mstBankAccountType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstBankAccountType->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstBankAccountType);
            $entityManager->flush();

            return $this->redirectToRoute('master_bank_account_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_bank_account_type' => $mstBankAccountType,
            'form' => $form->createView(),
            'index_path' => 'master_bank_account_type_index',
            'label_title' => 'label.bank_account_type',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstBankAccountType $mstBankAccountType
     * @return Response
     */
    public function edit(Request $request, MstBankAccountType $mstBankAccountType): Response
    {
        $form = $this->createForm(MstBankAccountTypeType::class, $mstBankAccountType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_bank_account_type_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_bank_account_type' => $mstBankAccountType,
            'form' => $form->createView(),
            'index_path' => 'master_bank_account_type_index',
            'label_title' => 'label.bank_account_type',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstBankAccountType $mstBankAccountType
     * @return Response
     */
    public function delete(Request $request, MstBankAccountType $mstBankAccountType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstBankAccountType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstBankAccountType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_bank_account_type_index');
    }
}
