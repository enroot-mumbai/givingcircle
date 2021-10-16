<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsFaq;
use App\Form\Cms\CmsFaqType;
use App\Repository\Cms\CmsFaqRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/core/cms/faq", name="cms_faq_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsFaqController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsFaqRepository $cmsFaqRepository
     * @return Response
     */
    public function index(CmsFaqRepository $cmsFaqRepository): Response
    {
        $faqs = $cmsFaqRepository->findAll();
        return $this->render('cms/cms_faq/index.html.twig', [
            'cmsfaqs' => $faqs,
            'path_index' => 'cms_faq_index',
            'path_add' => 'cms_faq_add',
            'path_edit' => 'cms_faq_edit',
            'path_faq_detail' => 'cms_faq_detail_index',
            'path_show' => 'cms_faq_show',
            'label_title' => 'label.faq',
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
        $mstFaq = new CmsFaq();
        $form = $this->createForm(CmsFaqType::class, $mstFaq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstFaq->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstFaq);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_faq_index');
        }
        return $this->render('form/form.html.twig', [
            'cmsfaq' => $mstFaq,
            'form' => $form->createView(),
            'index_path' => 'cms_faq_index',
            'label_title' => 'label.faq',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param CmsFaq $mstFaq
     * @return Response
     */
    public function show(CmsFaq $mstFaq): Response
    {
        return $this->render('cms/cms_faq/show.html.twig', [
            'data' => $mstFaq,
            'index_path' => 'cms_faq_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_faq_index',
            'path_edit' => 'cms_faq_edit',
            'path_delete' => 'cms_faq_delete',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsFaq $mstFaq
     * @return Response
     */
    public function edit(Request $request, CmsFaq $mstFaq): Response
    {
        $form = $this->createForm(CmsFaqType::class, $mstFaq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_faq_index');
        }
        return $this->render('form/form.html.twig', [
            'cmsfaq' => $mstFaq,
            'form' => $form->createView(),
            'index_path' => 'cms_faq_index',
            'label_title' => 'label.faq',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param CmsFaq $mstFaq
     * @return Response
     */
    public function delete(Request $request, CmsFaq $mstFaq): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstFaq->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstFaq);
            $entityManager->flush();
        }
        return $this->redirectToRoute('cms_faq_index');
    }
}
