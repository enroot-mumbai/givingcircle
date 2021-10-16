<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsUserTestimonial;
use App\Form\Cms\CmsUserTestimonialAddType;
use App\Form\Cms\CmsUserTestimonialType;
use App\Repository\Cms\CmsUserTestimonialRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/core/cms/usertestimonial", name="cms_usertestimonial_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsUserTestimonialController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsUserTestimonialRepository $cmsUserTestimonialRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(CmsUserTestimonialRepository $cmsUserTestimonialRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $cmsUserTestimonialRepository->getUserTestimonial();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('cms/cms_user_testimonial/index.html.twig', [
            'cms_user_testimonials' => $pagination,
            'path_index' => 'cms_usertestimonial_index',
            'path_add' => 'cms_usertestimonial_add',
            'path_edit' => 'cms_usertestimonial_edit',
            'path_show' => 'cms_usertestimonial_show',
            'label_title' => 'label.testimonial',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $cmsUserTestimonial = new CmsUserTestimonial();
        $form = $this->createForm(CmsUserTestimonialAddType::class, $cmsUserTestimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cmsUserTestimonial->setCreateDateTime(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsUserTestimonial);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_usertestimonial_index');
        }

        return $this->render('cms/cms_user_testimonial/form_add.html.twig', [
            'cms_user_testimonial' => $cmsUserTestimonial,
            'form' => $form->createView(),
            'index_path' => 'cms_usertestimonial_index',
            'label_title' => 'label.testimonial',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsUserTestimonial $cmsUserTestimonial
     * @return Response
     */
    public function edit(Request $request, CmsUserTestimonial $cmsUserTestimonial): Response
    {
        $form = $this->createForm(CmsUserTestimonialType::class, $cmsUserTestimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_usertestimonial_index');
        }

        return $this->render('cms/cms_user_testimonial/form.html.twig', [
            'cms_user_testimonial' => $cmsUserTestimonial,
            'form' => $form->createView(),
            'index_path' => 'cms_usertestimonial_index',
            'label_title' => 'label.testimonial',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/edit_all_details/{id}", name="edit_all_details", methods={"GET","POST"})
     * @param Request $request
     * @param CmsUserTestimonial $cmsUserTestimonial
     * @return Response
     */
    public function editAllDetails(Request $request, CmsUserTestimonial $cmsUserTestimonial): Response
    {
        $form = $this->createForm(CmsUserTestimonialAddType::class, $cmsUserTestimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_usertestimonial_index');
        }

        return $this->render('cms/cms_user_testimonial/form_add.html.twig', [
            'cms_user_testimonial' => $cmsUserTestimonial,
            'form' => $form->createView(),
            'index_path' => 'cms_usertestimonial_index',
            'label_title' => 'label.testimonial',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

}
