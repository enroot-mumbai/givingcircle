<?php

namespace App\Controller\Master;

use App\Entity\Master\MstCurrency;
use App\Form\Master\MstCurrencyType;
use App\Repository\Master\MstCurrencyRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("core/master/general/currency", name="master_currency_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstCurrencyController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstCurrencyRepository $mstCurrencyRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstCurrencyRepository $mstCurrencyRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstCurrencyRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_currency/index.html.twig', [
            'mst_currencies' => $pagination,
            'path_index' => 'master_currency_index',
            'path_add' => 'master_currency_add',
            'path_edit' => 'master_currency_edit',
            'path_show' => 'master_currency_show',
            'label_title' => 'label.currency',
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
        $mstCurrency = new MstCurrency();
        $form = $this->createForm(MstCurrencyType::class, $mstCurrency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstCurrency->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstCurrency);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_currency_index');
        }

        return $this->render('form/form.html.twig', [
            'mstcurrency' => $mstCurrency,
            'form' => $form->createView(),
            'index_path' => 'master_zodia_index',
            'label_title' => 'label.currency',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstCurrency $mstCurrency
     * @return Response
     */
    public function edit(Request $request, MstCurrency $mstCurrency): Response
    {
        $form = $this->createForm(MstCurrencyType::class, $mstCurrency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_currency_index');
        }

        return $this->render('form/form.html.twig', [
            'mstcurrency' => $mstCurrency,
            'form' => $form->createView(),
            'index_path' => 'master_currency_index',
            'label_title' => 'label.currency',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstCurrency $mstCurrency
     * @return Response
     */
    public function delete(Request $request, MstCurrency $mstCurrency): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstCurrency->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstCurrency);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_currency_index');
    }
}
