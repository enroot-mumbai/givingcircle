<?php

namespace App\Controller\Master;

use App\Entity\Master\MstPlaceOfWork;
use App\Form\Master\MstPlaceOfWorkType;
use App\Repository\Master\MstPlaceOfWorkRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/place_of_work" , name="master_place_of_work_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstPlaceOfWorkController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstPlaceOfWorkRepository $MstPlaceOfWorkRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstPlaceOfWorkRepository $MstPlaceOfWorkRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $MstPlaceOfWorkRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_place_of_work/index.html.twig', [
            'mst_place_of_works' => $pagination,
            'path_index' => 'master_place_of_work_index',
            'path_add' => 'master_place_of_work_add',
            'path_edit' => 'master_place_of_work_edit',
            'path_show' => 'master_place_of_work_show',
            'label_title' => 'label.place_of_work',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstPlaceOfWorkRepository $MstPlaceOfWorkRepository
     * @return Response
     */
    public function new(Request $request, MstPlaceOfWorkRepository $MstPlaceOfWorkRepository): Response
    {
        $MstPlaceOfWork = new MstPlaceOfWork();
        $form = $this->createForm(MstPlaceOfWorkType::class, $MstPlaceOfWork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $MstPlaceOfWork->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($MstPlaceOfWork);
            $entityManager->flush();

            return $this->redirectToRoute('master_place_of_work_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_place_of_work' => $MstPlaceOfWork,
            'form' => $form->createView(),
            'index_path' => 'master_place_of_work_index',
            'label_title' => 'label.place_of_work',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstPlaceOfWork $MstPlaceOfWork
     * @return Response
     */
    public function edit(Request $request, MstPlaceOfWork $MstPlaceOfWork): Response
    {
        $form = $this->createForm(MstPlaceOfWorkType::class, $MstPlaceOfWork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_place_of_work_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_place_of_work' => $MstPlaceOfWork,
            'form' => $form->createView(),
            'index_path' => 'master_place_of_work_index',
            'label_title' => 'label.place_of_work',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstPlaceOfWork $MstPlaceOfWork
     * @return Response
     */
    public function delete(Request $request, MstPlaceOfWork $MstPlaceOfWork): Response
    {
        if ($this->isCsrfTokenValid('delete'.$MstPlaceOfWork->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($MstPlaceOfWork);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_place_of_work_index');
    }
}
