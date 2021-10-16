<?php

namespace App\Controller\Master;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Master\MstCity;
use App\Form\Master\MstCityType;
use App\Repository\Master\MstCityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/master/place/city", name="master_city_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstCityController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstCityRepository $mstCityRepository
     * @param Request $request
     * @return Response
     */
    public function index(MstCityRepository $mstCityRepository, Request $request): Response
    {
        $city = $mstCityRepository->findBy(['mstCountry' => $request->query->get('country_id')]);
        return $this->render('master/mst_city/index.html.twig', [
            'mst_cities' => $city,
            'path_index' => 'master_city_index',
            'path_add' => 'master_city_add',
            'path_edit' => 'master_city_edit',
            'path_show' => 'master_city_show',
            'label_title' => 'label.city',
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
        $mstCity = new MstCity();
        $form = $this->createForm(MstCityType::class, $mstCity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstCity);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_city_index');
        }

        return $this->render('master/mst_city/form.html.twig', [
            'master_city' => $mstCity,
            'form' => $form->createView(),
            'index_path' => 'master_city_index',
            'label_title' => 'label.mastercity',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $countryId = trim($request->query->get('countryId'));
        $city = ucwords($request->query->get('citySearch'));

        $mstCity = $this->getDoctrine()->getRepository(MstCity::class)->getCityListByCountryId($city, $countryId);
        return $this->render('master/mst_city/_ajax_listing.html.twig', [
            'mst_cities' => $mstCity,
            'country_id' => $countryId,
            'path_add' => 'master_city_add',
            'path_edit' => 'master_city_edit',
            'path_show' => 'master_city_show',
            'label_title' => 'label.city',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstCity $mstCity
     * @return Response
     */
    public function edit(Request $request, MstCity $mstCity): Response
    {
        $form = $this->createForm(MstCityType::class, $mstCity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_city_index');
        }

        return $this->render('master/mst_city/form.html.twig', [
            'master_city' => $mstCity,
            'form' => $form->createView(),
            'index_path' => 'master_city_index',
            'label_title' => 'label.mastercity',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstCity $mstCity
     * @return Response
     */
    public function delete(Request $request, MstCity $mstCity): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstCity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstCity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_city_index');
    }
}
