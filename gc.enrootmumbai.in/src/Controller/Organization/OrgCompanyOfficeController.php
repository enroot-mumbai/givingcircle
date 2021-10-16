<?php

namespace App\Controller\Organization;

use App\Entity\Organization\OrgCompany;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Organization\OrgCompanyOffice;
use App\Form\Organization\OrgCompanyOfficeType;
use App\Repository\Organization\OrgCompanyOfficeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/core/organization/office", name="org_company_office_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class OrgCompanyOfficeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param OrgCompanyOfficeRepository $orgCompanyOfficeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(OrgCompanyOfficeRepository $orgCompanyOfficeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $company_id = $request->query->get('company_id');
        if(!$company_id) {
            return $this->redirectToRoute('org_company_index');
            }
        $orgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($company_id);
        $queryBuilder = $orgCompanyOfficeRepository->findBy(['orgCompany' => $company_id]);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('organization/org_company_office/index.html.twig', [
            'org_company_offices' => $pagination,
            'org_company' => $orgCompany,
            'path_index' => 'org_company_office_index',
            'path_add' => 'org_company_office_add',
            'path_edit' => 'org_company_office_edit',
            'path_show' => 'org_company_office_show',
            'label_title' => 'label.company_office',
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
        $company_id = $request->query->get('company_id');
        if(!$company_id) {
            return $this->redirectToRoute('org_company_index');
        }

        $orgCompanyOffice = new OrgCompanyOffice();
        $orgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($request->query->get('company_id'));
        $orgCompanyOffice->setOrgCompany($orgCompany);

        $form = $this->createForm(OrgCompanyOfficeType::class, $orgCompanyOffice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orgCompanyOffice->setRowId(Uuid::uuid4()->toString());
             $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($orgCompanyOffice);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('org_company_office_index', $request->query->all());
        }

        return $this->render('organization/org_company_office/form.html.twig', [
            'org_company_office' => $orgCompanyOffice,
            'form' => $form->createView(),
            'index_path' => 'org_company_office_index',
            'label_title' => 'label.company_office',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param OrgCompanyOffice $orgCompanyOffice
     * @param Request $request
     * @return Response
     */
    public function show(OrgCompanyOffice $orgCompanyOffice, Request $request): Response
    {
        $company_id = $request->query->get('company_id');
        if(!$company_id) {
            return $this->redirectToRoute('org_company_index');
        }

        return $this->render('organization/org_company_office/show.html.twig', [
            'data' => $orgCompanyOffice,
            'label_title' => 'label.company_office',
            'label_button' => 'label.update',
            'path_index' => 'org_company_office_index',
            'path_edit' => 'org_company_office_edit',
            'path_delete' => 'org_company_office_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param OrgCompanyOffice $orgCompanyOffice
     * @return Response
     */
    public function edit(Request $request, OrgCompanyOffice $orgCompanyOffice): Response
    {
        $company_id = $request->query->get('company_id');
        if(!$company_id) {
            return $this->redirectToRoute('org_company_index');
        }

        $form = $this->createForm(OrgCompanyOfficeType::class, $orgCompanyOffice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('org_company_office_index', $request->query->all());
        }

        return $this->render('organization/org_company_office/form.html.twig', [
            'org_company_office' => $orgCompanyOffice,
            'form' => $form->createView(),
            'index_path' => 'org_company_office_index',
            'label_title' => 'label.company_office',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param OrgCompanyOffice $orgCompanyOffice
     * @return Response
     */
    public function delete(Request $request, OrgCompanyOffice $orgCompanyOffice): Response
    {
        $company_id = $request->query->get('company_id');
        if(!$company_id) {
            return $this->redirectToRoute('org_company_index');
        }

        if ($this->isCsrfTokenValid('delete'.$orgCompanyOffice->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($orgCompanyOffice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('org_company_office_index', $request->query->all());
    }
}
