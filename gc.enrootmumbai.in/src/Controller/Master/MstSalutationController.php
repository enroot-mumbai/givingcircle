<?php

namespace App\Controller\Master;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Master\MstSalutation;
use App\Form\Master\MstSalutationType;
use App\Repository\Master\MstSalutationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/core/master/general/salutation", name="master_salutation_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */

class MstSalutationController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstSalutationRepository $mstSalutationRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(MstSalutationRepository $mstSalutationRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $mstSalutationRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_salutation/index.html.twig', [
            'mst_salutations' => $pagination,
            'path_index' => 'master_salutation_index',
            'path_add' => 'master_salutation_add',
            'path_edit' => 'master_salutation_edit',
            'path_show' => 'master_salutation_show',
            'label_title' => 'label.salutation',
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
        $mstSalutation = new MstSalutation();
        $form = $this->createForm(MstSalutationType::class, $mstSalutation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstSalutation->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstSalutation);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_salutation_index');
        }
        return $this->render('form/form.html.twig', [
            'mst_salutation' => $mstSalutation,
            'form' => $form->createView(),
            'index_path' => 'master_salutation_index',
            'label_title' => 'label.salutation',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstSalutation $mstSalutation
     * @return Response
     */
    public function edit(Request $request, MstSalutation $mstSalutation): Response
    {
        $form = $this->createForm(MstSalutationType::class, $mstSalutation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_salutation_index');
        }
        return $this->render('form/form.html.twig', [
            'mst_salutation' => $mstSalutation,
            'form' => $form->createView(),
            'index_path' => 'master_salutation_index',
            'label_title' => 'label.salutation',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstSalutation $mstSalutation
     * @return Response
     */
    public function delete(Request $request, MstSalutation $mstSalutation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstSalutation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstSalutation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_salutation_index');
    }
}
