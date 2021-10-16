<?php

namespace App\Controller\Transaction;

use App\Entity\Organization\OrgCompany;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCollectionCentreDetails;
use App\Form\Transaction\TrnCollectionCentreDetailsType;
use App\Repository\Transaction\TrnCollectionCentreDetailsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use App\Service\FileUploaderHelper;

/**
 * @Route("/core/product/collection_centre", name="product_collection_centre_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class TrnCollectionCentreController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param TrnCollectionCentreDetailsRepository $trnCollectionCentreDetailsRepository
     * @return Response
     */
    public function index(TrnCollectionCentreDetailsRepository $trnCollectionCentreDetailsRepository): Response
    {
        return $this->render('transaction/collection_centre/index.html.twig', [
            'collection_centres' => $trnCollectionCentreDetailsRepository->findAll(),
            'path_index' => 'product_collection_centre_index',
            'path_add' => 'product_collection_centre_add',
            'path_edit' => 'product_collection_centre_edit',
            'path_show' => 'product_collection_centre_show',
            'label_title' => 'label.collection_centre',
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
        $trnCollectionCentreDetails = new TrnCollectionCentreDetails();
        $form = $this->createForm(TrnCollectionCentreDetailsType::class, $trnCollectionCentreDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trnCollectionCentreDetails->setCreatedOn(new \DateTime());
            $trnCollectionCentreDetails->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trnCollectionCentreDetails);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('product_collection_centre_index');
        }

        return $this->render('transaction/collection_centre/form.html.twig', [
            'collection_centre' => $trnCollectionCentreDetails,
            'form' => $form->createView(),
            'label_title' => 'label.collection_centre',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param TrnCollectionCentreDetails $trnCollectionCentreDetails
     * @return Response
     */
    public function show(TrnCollectionCentreDetails $trnCollectionCentreDetails): Response
    {
        return $this->render('transaction/collection_centre/show.html.twig', [
            'data' => $trnCollectionCentreDetails,
            'label_title' => 'label.company',
            'label_button' => 'label.update',
            'path_index' => 'collection_centre_index',
            'path_edit' => 'collection_centre_edit',
            'path_delete' => 'collection_centre_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCollectionCentreDetails $trnCollectionCentreDetails
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, TrnCollectionCentreDetails $trnCollectionCentreDetails, FileUploaderHelper $fileUploaderHelper): Response
    {

        $form = $this->createForm(TrnCollectionCentreDetailsType::class, $trnCollectionCentreDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trnCollectionCentreDetails->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trnCollectionCentreDetails);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('product_collection_centre_index');
        }

        return $this->render('transaction/collection_centre/form.html.twig', [
            'collection_centre' => $trnCollectionCentreDetails,
            'form' => $form->createView(),
            'label_title' => 'label.collection_centre',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param TrnCollectionCentreDetails $trnCollectionCentreDetails
     * @return Response
     */
    public function delete(Request $request, TrnCollectionCentreDetails $trnCollectionCentreDetails): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trnCollectionCentreDetails->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trnCollectionCentreDetails);
            $entityManager->flush();
        }

        return $this->redirectToRoute('collection_centre_index');
    }
}
