<?php

namespace App\Controller\Master;

use App\Service\CommonHelper;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Master\MstSocial;
use App\Form\Master\MstSocialType;
use App\Repository\Master\MstSocialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/master/general/social", name="master_social_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */

class MstSocialController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstSocialRepository $mstSocialRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(MstSocialRepository $mstSocialRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $mstSocialRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_social/index.html.twig', [
            'mst_socials' => $pagination,
            'path_index' => 'master_social_index',
            'path_add' => 'master_social_add',
            'path_edit' => 'master_social_edit',
            'path_show' => 'master_social_show',
            'label_title' => 'label.social',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param CommonHelper $commonHelper
     * @return Response
     */
    public function new(Request $request, CommonHelper $commonHelper): Response
    {
        $mstSocial = new MstSocial();
        $form = $this->createForm(MstSocialType::class, $mstSocial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstSocial->setSocialValue($commonHelper->slugify($form->get('social')->getData()));
            $mstSocial->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstSocial);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_social_index');
        }
        return $this->render('form/form.html.twig', [
            'mst_social' => $mstSocial,
            'form' => $form->createView(),
            'index_path' => 'master_social_index',
            'label_title' => 'label.social',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstSocial $mstSocial
     * @return Response
     */
    public function edit(Request $request, MstSocial $mstSocial): Response
    {
        $form = $this->createForm(MstSocialType::class, $mstSocial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_social_index');
        }
        return $this->render('form/form.html.twig', [
            'mst_social' => $mstSocial,
            'form' => $form->createView(),
            'index_path' => 'master_social_index',
            'label_title' => 'label.social',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstSocial $mstSocial
     * @return Response
     */
    public function delete(Request $request, MstSocial $mstSocial): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstSocial->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstSocial);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_social_index');
    }
}
