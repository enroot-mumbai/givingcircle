<?php

namespace App\Controller\Master;

use App\Entity\Master\MstTypeOfOrganization;
use App\Form\Master\MstTypeOfOrganizationType;
use App\Repository\Master\MstTypeOfOrganizationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/type_of_organization" , name="master_type_of_organization_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstTypeOfOrganizationController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstTypeOfOrganizationRepository $MstTypeOfOrganizationRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstTypeOfOrganizationRepository $mstTypeOfOrganizationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstTypeOfOrganizationRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_type_of_organization/index.html.twig', [
            'mst_type_of_organizations' => $pagination,
            'path_index' => 'master_type_of_organization_index',
            'path_add' => 'master_type_of_organization_add',
            'path_edit' => 'master_type_of_organization_edit',
            'path_show' => 'master_type_of_organization_show',
            'label_title' => 'label.type_of_organization',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstTypeOfOrganizationRepository $mstTypeOfOrganizationRepository
     * @return Response
     */
    public function new(Request $request, MstTypeOfOrganizationRepository $mstTypeOfOrganizationRepository): Response
    {
        $mstTypeOfOrganization = new MstTypeOfOrganization();
        $form = $this->createForm(MstTypeOfOrganizationType::class, $mstTypeOfOrganization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstTypeOfOrganization->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstTypeOfOrganization);
            $entityManager->flush();

            return $this->redirectToRoute('master_type_of_organization_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_type_of_organization' => $mstTypeOfOrganization,
            'form' => $form->createView(),
            'index_path' => 'master_type_of_organization_index',
            'label_title' => 'label.type_of_organization',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstTypeOfOrganization $mstTypeOfOrganization
     * @return Response
     */
    public function edit(Request $request, MstTypeOfOrganization $mstTypeOfOrganization): Response
    {
        $form = $this->createForm(MstTypeOfOrganizationType::class, $mstTypeOfOrganization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_type_of_organization_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_type_of_organization' => $mstTypeOfOrganization,
            'form' => $form->createView(),
            'index_path' => 'master_type_of_organization_index',
            'label_title' => 'label.type_of_organization',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstTypeOfOrganization $mstTypeOfOrganization
     * @return Response
     */
    public function delete(Request $request, MstTypeOfOrganization $mstTypeOfOrganization): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstTypeOfOrganization->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstTypeOfOrganization);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_type_of_organization_index');
    }
}
