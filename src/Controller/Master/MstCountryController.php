<?php

namespace App\Controller\Master;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Master\MstCountry;
use App\Form\Master\MstCountryType;
use App\Repository\Master\MstCountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/master/place/country", name="master_country_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstCountryController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstCountryRepository $mstCountryRepository
     * @return Response
     */
    public function index(MstCountryRepository $mstCountryRepository): Response
    {
        $mstCountries = $mstCountryRepository->findAll();
        return $this->render('master/mst_country/index.html.twig', [
            'mst_countries' => $mstCountries,
            'path_index' => 'master_country_index',
            'path_add' => 'master_country_add',
            'path_edit' => 'master_country_edit',
            'path_show' => 'master_country_show',
            'label_title' => 'label.country',
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
        $mstCountry = new MstCountry();
        $form = $this->createForm(MstCountryType::class, $mstCountry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstCountry);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_country_index');
        }

        return $this->render('form/form.html.twig', [
            'master_country' => $mstCountry,
            'form' => $form->createView(),
            'index_path' => 'master_country_index',
            'label_title' => 'label.country',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstCountry $mstCountry
     * @return Response
     */
    public function edit(Request $request, MstCountry $mstCountry): Response
    {
        $form = $this->createForm(MstCountryType::class, $mstCountry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_country_index');
        }

        return $this->render('form/form.html.twig', [
            'master_country' => $mstCountry,
            'form' => $form->createView(),
            'index_path' => 'master_country_index',
            'label_title' => 'label.country',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstCountry $mstCountry
     * @return Response
     */
    public function delete(Request $request, MstCountry $mstCountry): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstCountry->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstCountry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_country_index');
    }

}
