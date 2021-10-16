<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsFaq;
use App\Entity\Cms\CmsFaqDetail;
use App\Form\Cms\CmsFaqDetailType;
use App\Repository\Cms\CmsFaqDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/cms/faq_detail", name="cms_faq_detail_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsFaqDetailController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsFaqDetailRepository $cmsFaqDetailRepository
     * @param Request $request
     * @return Response
     */
    public function index(CmsFaqDetailRepository $cmsFaqDetailRepository, Request $request): Response
    {
        $faq_id = $request->query->get('faq_id');
        if(!$faq_id) {
            return $this->redirectToRoute('cms_faq_index');
        }
        $faqdetails = $cmsFaqDetailRepository->findBy(['cmsFaq' => $faq_id], ['sequenceNo' => 'ASC']);
        return $this->render('cms/cms_faq_detail/index.html.twig', [
            'cms_faq_details' => $faqdetails,
            'path_index' => 'cms_faq_detail_index',
            'path_add' => 'cms_faq_detail_add',
            'path_edit' => 'cms_faq_detail_edit',
            'path_show' => 'cms_faq_detail_show',
            'label_title' => 'label.faq_detail',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param CmsFaqDetailRepository $cmsFaqDetailRepository
     * @return Response
     */
    public function new(Request $request, CmsFaqDetailRepository $cmsFaqDetailRepository): Response
    {
        $faq_id = $request->query->get('faq_id');
        if(!$faq_id) {
            return $this->redirectToRoute('cms_faq_index');
        }
        $cmsFaqDetail = new CmsFaqDetail();
        $cmsFaq = $this->getDoctrine()->getRepository(CmsFaq::class)->find($faq_id);
        $sequenceNo = $cmsFaqDetailRepository->findOneBySeqNo($faq_id);
        $cmsFaqDetail->setSequenceNo(($sequenceNo[1] + 1));
        $cmsFaqDetail->setCmsFaq($cmsFaq);
        $form = $this->createForm(CmsFaqDetailType::class, $cmsFaqDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsFaqDetail);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_faq_detail_index', $request->query->all());
        }

        return $this->render('cms/cms_faq_detail/form.html.twig', [
            'cms_faq_detail' => $cmsFaqDetail,
            'form' => $form->createView(),
            'index_path' => 'cms_faq_detail_index',
            'label_title' => 'label.faq_detail',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param CmsFaqDetail $cmsFaqDetail
     * @param Request $request
     * @return Response
     */
    public function show(CmsFaqDetail $cmsFaqDetail, Request $request): Response
    {
        $faq_id = $request->query->get('faq_id');
        if(!$faq_id) {
            return $this->redirectToRoute('cms_faq_index');
        }
        return $this->render('cms/cms_faq_detail/show.html.twig', [
            'data' => $cmsFaqDetail,
            'index_path' => 'cms_faq_detail_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_faq_detail_index',
            'path_edit' => 'cms_faq_detail_edit',
            'path_delete' => 'cms_faq_detail_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsFaqDetail $cmsFaqDetail
     * @return Response
     */
    public function edit(Request $request, CmsFaqDetail $cmsFaqDetail): Response
    {
        $faq_id = $request->query->get('faq_id');
        if(!$faq_id) {
            return $this->redirectToRoute('cms_faq_index');
        }
        $form = $this->createForm(CmsFaqDetailType::class, $cmsFaqDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_faq_detail_index', $request->query->all());
        }

        return $this->render('cms/cms_faq_detail/form.html.twig', [
            'cms_faq_detail' => $cmsFaqDetail,
            'form' => $form->createView(),
            'index_path' => 'cms_faq_detail_index',
            'label_title' => 'label.faq_detail',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param CmsFaqDetail $cmsFaqDetail
     * @return Response
     */
    public function delete(Request $request, CmsFaqDetail $cmsFaqDetail): Response
    {
        $faq_id = $request->query->get('faq_id');
        if(!$faq_id) {
            return $this->redirectToRoute('cms_faq_index');
        }
        if ($this->isCsrfTokenValid('delete'.$cmsFaqDetail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsFaqDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_faq_detail_index', $request->query->all());
    }
}
