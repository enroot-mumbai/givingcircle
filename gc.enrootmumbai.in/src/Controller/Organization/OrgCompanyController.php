<?php

namespace App\Controller\Organization;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Organization\OrgCompany;
use App\Form\Organization\OrgCompanyType;
use App\Repository\Organization\OrgCompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use App\Service\FileUploaderHelper;

/**
 * @Route("/core/organization/company", name="org_company_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class OrgCompanyController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param OrgCompanyRepository $orgCompanyRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(OrgCompanyRepository $orgCompanyRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $orgCompanyRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('organization/org_company/index.html.twig', [
            'org_companies' => $pagination,
            'path_index' => 'org_company_index',
            'path_add' => 'org_company_add',
            'path_edit' => 'org_company_edit',
            'path_show' => 'org_company_show',
            'label_title' => 'label.company',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileUploaderHelper $fileUploaderHelper): Response
    {
        $orgCompany = new OrgCompany();
        $form = $this->createForm(OrgCompanyType::class, $orgCompany);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $orgCompany->setRowId(Uuid::uuid4()->toString());
            $file = $form['companyLogo']->getData();
            if ($file) {
                if ($orgCompany->getCompanyLogo() != '') {
                    $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $orgCompany->getCompanyLogo());
                } else {
                    $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $logo = null);
                }
                $orgCompany->setCompanyLogo($newFilename);
                $orgCompany->setCompanyLogoFilePath($this->getParameter('public_file_folder'));
            }

            $entityManager->persist($orgCompany);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('org_company_index');
        }

        return $this->render('organization/org_company/form.html.twig', [
            'org_company' => $orgCompany,
            'form' => $form->createView(),
            'label_title' => 'label.company',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param OrgCompany $orgCompany
     * @return Response
     */
    public function show(OrgCompany $orgCompany): Response
    {
        return $this->render('organization/org_company/show.html.twig', [
            'data' => $orgCompany,
            'label_title' => 'label.company',
            'label_button' => 'label.update',
            'path_index' => 'org_company_index',
            'path_edit' => 'org_company_edit',
            'path_delete' => 'org_company_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param OrgCompany $orgCompany
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, OrgCompany $orgCompany, FileUploaderHelper $fileUploaderHelper): Response
    {

        $form = $this->createForm(OrgCompanyType::class, $orgCompany);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['companyLogo']->getData();
            if ($file) {
                if ($orgCompany->getCompanyLogo() != '') {
                    $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $orgCompany->getCompanyLogo());
                } else {
                    $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $logo = null);
                }
                $orgCompany->setCompanyLogo($newFilename);
                $orgCompany->setCompanyLogoFilePath($this->getParameter('public_file_folder'));
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('org_company_index');
        }

        return $this->render('organization/org_company/form.html.twig', [
            'org_company' => $orgCompany,
            'form' => $form->createView(),
            'label_title' => 'label.company',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param OrgCompany $orgCompany
     * @return Response
     */
    public function delete(Request $request, OrgCompany $orgCompany): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orgCompany->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($orgCompany);
            $entityManager->flush();
        }

        return $this->redirectToRoute('org_company_index');
    }
}
