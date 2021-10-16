<?php

namespace App\Controller\Master;

use App\Entity\Master\MstSourceOfInformation;
use App\Form\Master\MstSourceOfInformationType;
use App\Repository\Master\MstSourceOfInformationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/source_of_information" , name="master_source_of_information_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstSourceOfInformationController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstSourceOfInformationRepository $mstSourceOfInformationRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstSourceOfInformationRepository $mstSourceOfInformationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstSourceOfInformationRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_source_of_information/index.html.twig', [
            'mst_source_of_informations' => $pagination,
            'path_index' => 'master_source_of_information_index',
            'path_add' => 'master_source_of_information_add',
            'path_edit' => 'master_source_of_information_edit',
            'path_show' => 'master_source_of_information_show',
            'label_title' => 'label.source_of_information',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstSourceOfInformationRepository $mstSourceOfInformationRepository
     * @return Response
     */
    public function new(Request $request, MstSourceOfInformationRepository $mstSourceOfInformationRepository): Response
    {
        $mstSourceOfInformation = new MstSourceOfInformation();
        $form = $this->createForm(MstSourceOfInformationType::class, $mstSourceOfInformation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstSourceOfInformation->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstSourceOfInformation);
            $entityManager->flush();

            return $this->redirectToRoute('master_source_of_information_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_source_of_information' => $mstSourceOfInformation,
            'form' => $form->createView(),
            'index_path' => 'master_source_of_information_index',
            'label_title' => 'label.source_of_information',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstSourceOfInformation $mstSourceOfInformation
     * @return Response
     */
    public function edit(Request $request, MstSourceOfInformation $mstSourceOfInformation): Response
    {
        $form = $this->createForm(MstSourceOfInformationType::class, $mstSourceOfInformation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_source_of_information_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_source_of_information' => $mstSourceOfInformation,
            'form' => $form->createView(),
            'index_path' => 'master_source_of_information_index',
            'label_title' => 'label.source_of_information',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstSourceOfInformation $mstSourceOfInformation
     * @return Response
     */
    public function delete(Request $request, MstSourceOfInformation $mstSourceOfInformation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstSourceOfInformation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstSourceOfInformation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_source_of_information_index');
    }
}
