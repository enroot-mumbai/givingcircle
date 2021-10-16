<?php

namespace App\Controller\Master;

use App\Entity\Master\MstGender;
use App\Form\Master\MstGenderType;
use App\Repository\Master\MstGenderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/gender" , name="master_gender_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstGenderController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstGenderRepository $MstGenderRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstGenderRepository $mstGenderRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstGenderRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_gender/index.html.twig', [
            'mst_genders' => $pagination,
            'path_index' => 'master_gender_index',
            'path_add' => 'master_gender_add',
            'path_edit' => 'master_gender_edit',
            'path_show' => 'master_gender_show',
            'label_title' => 'label.gender',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstGenderRepository $mstGenderRepository
     * @return Response
     */
    public function new(Request $request, MstGenderRepository $mstGenderRepository): Response
    {
        $mstGender = new MstGender();
        $form = $this->createForm(MstGenderType::class, $mstGender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstGender->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstGender);
            $entityManager->flush();

            return $this->redirectToRoute('master_gender_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_gender' => $mstGender,
            'form' => $form->createView(),
            'index_path' => 'master_gender_index',
            'label_title' => 'label.gender',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstGender $mstGender
     * @return Response
     */
    public function edit(Request $request, MstGender $mstGender): Response
    {
        $form = $this->createForm(MstGenderType::class, $mstGender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_gender_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_gender' => $mstGender,
            'form' => $form->createView(),
            'index_path' => 'master_gender_index',
            'label_title' => 'label.gender',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstGender $mstGender
     * @return Response
     */
    public function delete(Request $request, MstGender $mstGender): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstGender->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstGender);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_gender_index');
    }
}
