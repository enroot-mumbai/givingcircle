<?php

namespace App\Controller\Master;

use App\Entity\Master\MstAreasInCity;
use App\Form\Master\MstAreasInCityType;
use App\Repository\Master\MstAreasInCityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/place/area_city" , name="master_area_city_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstAreasInCityController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstAreasInCityRepository $mstAreasInCityRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstAreasInCityRepository $mstAreasInCityRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstAreasInCityRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_area_city/index.html.twig', [
            'mst_area_cities' => $pagination,
            'path_index' => 'master_area_city_index',
            'path_add' => 'master_area_city_add',
            'path_edit' => 'master_area_city_edit',
            'path_show' => 'master_area_city_show',
            'label_title' => 'label.area_city',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstAreasInCityRepository $mstAreasInCityRepository
     * @return Response
     */
    public function new(Request $request, MstAreasInCityRepository $mstAreasInCityRepository): Response
    {
        $mstAreasInCity = new MstAreasInCity();
        $form = $this->createForm(MstAreasInCityType::class, $mstAreasInCity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstAreasInCity->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstAreasInCity);
            $entityManager->flush();

            return $this->redirectToRoute('master_area_city_index');
        }

        return $this->render('master/mst_area_city/form.html.twig', [
            'mst_area_cities' => $mstAreasInCity,
            'form' => $form->createView(),
            'index_path' => 'master_area_city_index',
            'label_title' => 'label.area_city',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstAreasInCity $mstAreasInCity
     * @return Response
     */
    public function edit(Request $request, MstAreasInCity $mstAreasInCity): Response
    {
        $form = $this->createForm(MstAreasInCityType::class, $mstAreasInCity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_area_city_index');
        }

        return $this->render('master/mst_area_city/form.html.twig', [
            'mst_area_cities' => $mstAreasInCity,
            'form' => $form->createView(),
            'index_path' => 'master_area_city_index',
            'label_title' => 'label.area_city',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstAreasInCity $mstAreasInCity
     * @return Response
     */
    public function delete(Request $request, MstAreasInCity $mstAreasInCity): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstAreasInCity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstAreasInCity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_area_city_index');
    }
}
