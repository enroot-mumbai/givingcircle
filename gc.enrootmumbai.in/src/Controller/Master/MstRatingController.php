<?php

namespace App\Controller\Master;

use App\Entity\Master\MstRating;
use App\Form\Master\MstRatingType;
use App\Repository\Master\MstRatingRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("core/master/general/rating" , name="master_rating_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstRatingController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstRatingRepository $mstRatingRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstRatingRepository $mstRatingRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstRatingRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_rating/index.html.twig', [
            'mstratings' => $pagination,
            'path_index' => 'master_rating_index',
            'path_add' => 'master_rating_add',
            'path_edit' => 'master_rating_edit',
            'path_show' => 'master_rating_show',
            'label_title' => 'label.rating',
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
        $mstRating = new MstRating();
        $form = $this->createForm(MstRatingType::class, $mstRating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstRating->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstRating);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_rating_index');
        }

        return $this->render('form/form.html.twig', [
            'mstrating' => $mstRating,
            'form' => $form->createView(),
            'index_path' => 'master_rating_index',
            'label_title' => 'label.rating',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstRating $mstRating
     * @return Response
     */
    public function edit(Request $request, MstRating $mstRating): Response
    {
        $form = $this->createForm(MstRatingType::class, $mstRating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_rating_index');
        }

        return $this->render('form/form.html.twig', [
            'mstrating' => $mstRating,
            'form' => $form->createView(),
            'index_path' => 'master_rating_index',
            'label_title' => 'label.rating',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstRating $mstRating
     * @return Response
     */
    public function delete(Request $request, MstRating $mstRating): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstRating->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstRating);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_rating_index');
    }
}
