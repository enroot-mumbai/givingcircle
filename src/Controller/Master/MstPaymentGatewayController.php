<?php

namespace App\Controller\Master;

use App\Entity\Master\MstPaymentGateway;
use App\Form\Master\MstPaymentGatewayType;
use App\Repository\Master\MstPaymentGatewayRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/commercial/payment_gateway" , name="master_payment_gateway_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstPaymentGatewayController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstPaymentGatewayRepository $mstPaymentGatewayRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstPaymentGatewayRepository $mstPaymentGatewayRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstPaymentGatewayRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_payment_gateway/index.html.twig', [
            'mst_payment_gateways' => $pagination,
            'path_index' => 'master_payment_gateway_index',
            'path_add' => 'master_payment_gateway_add',
            'path_edit' => 'master_payment_gateway_edit',
            'path_show' => 'master_payment_gateway_show',
            'label_title' => 'label.payment_gateway',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstPaymentGatewayRepository $mstPaymentGatewayRepository
     * @return Response
     */
    public function new(Request $request, MstPaymentGatewayRepository $mstPaymentGatewayRepository): Response
    {
        $mstPaymentGateway = new MstPaymentGateway();
        $form = $this->createForm(MstPaymentGatewayType::class, $mstPaymentGateway);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstPaymentGateway->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstPaymentGateway);
            $entityManager->flush();

            return $this->redirectToRoute('master_payment_gateway_index');
        }

        return $this->render('master/mst_payment_gateway/form.html.twig', [
            'mst_payment_gateway' => $mstPaymentGateway,
            'form' => $form->createView(),
            'index_path' => 'master_payment_gateway_index',
            'label_title' => 'label.payment_gateway',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstPaymentGateway $mstPaymentGateway
     * @return Response
     */
    public function edit(Request $request, MstPaymentGateway $mstPaymentGateway): Response
    {
        $form = $this->createForm(MstPaymentGatewayType::class, $mstPaymentGateway);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_payment_gateway_index');
        }

        return $this->render('master/mst_payment_gateway/form.html.twig', [
            'mst_payment_gateway' => $mstPaymentGateway,
            'form' => $form->createView(),
            'index_path' => 'master_payment_gateway_index',
            'label_title' => 'label.payment_gateway',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstPaymentGateway $mstPaymentGateway
     * @return Response
     */
    public function delete(Request $request, MstPaymentGateway $mstPaymentGateway): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstPaymentGateway->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstPaymentGateway);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_payment_gateway_index');
    }

    /**
     * @Route("/{id}", name="show", methods={"GET","POST"})
     * @param MstPaymentGateway $mstPaymentGateway
     * @return Response
     */
    public function show(MstPaymentGateway $mstPaymentGateway): Response
    {
        return $this->render('master/mst_payment_gateway/show.html.twig', [
            'data' => $mstPaymentGateway,
            'label_title' => 'label.payment_gateway',
            'label_button' => 'label.update',
            'label_log' => 'label.log',
            'path_index' => 'master_payment_gateway_index',
            'path_edit' => 'master_payment_gateway_edit',
        ]);
    }
}
