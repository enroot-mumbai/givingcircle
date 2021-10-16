<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsMedia;
use App\Form\Cms\CmsMediaType;
use App\Repository\Cms\CmsMediaRepository;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/cms/media", name="cms_media_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsMediaController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsMediaRepository $cmsMediaRepository
     * @return Response
     */
    public function index(CmsMediaRepository $cmsMediaRepository): Response
    {
        return $this->render('cms/cms_media/index.html.twig', [
            'cms_medias' => $cmsMediaRepository->findAll(),
            'path_index' => 'cms_media_index',
            'path_add' => 'cms_media_add',
            'path_edit' => 'cms_media_edit',
            'path_show' => 'cms_media_show',
            'label_title' => 'label.media',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param CmsMediaRepository $cmsMediaRepository
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     */
    public function new(Request $request, CmsMediaRepository $cmsMediaRepository, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $cmsMedia = new CmsMedia();
        $option = array('image_required' => false);
        $sequenceNo = $cmsMediaRepository->findOneBySeqNo();
        $cmsMedia->setSequenceNo(($sequenceNo[1] + 1));
        $form = $this->createForm(CmsMediaType::class, $cmsMedia, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cmsMedia->setRowId(Uuid::uuid4()->toString());
            $imageFile = $form['mediaImage']->getData();
            if ($imageFile)
            {
                $newFilename = $fileUploaderHelper->uploadPublicFile($imageFile);
                $cmsMedia->setMediaImage($newFilename);
                $cmsMedia->setMediaImagePath($this->getParameter('public_file_folder'));
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsMedia);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_media_index');
        }

        return $this->render('cms/cms_media/form.html.twig', [
            'cms_media' => $cmsMedia,
            'form' => $form->createView(),
            'index_path' => 'cms_media_index',
            'label_title' => 'label.media',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param CmsMedia $cmsMedia
     * @return Response
     */
    public function show(CmsMedia $cmsMedia): Response
    {
        return $this->render('cms/cms_media/show.html.twig', [
            'data' => $cmsMedia,
            'index_path' => 'cms_media_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_media_index',
            'path_edit' => 'cms_media_edit',
            'path_delete' => 'cms_media_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsMedia $cmsMedia
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     */
    public function edit(Request $request, CmsMedia $cmsMedia, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $option = array('image_required' => false);
        $form = $this->createForm(CmsMediaType::class, $cmsMedia, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['mediaImage']->getData();
            if ($imageFile)
            {
                $newFilename = $fileUploaderHelper->uploadPublicFile($imageFile);
                $cmsMedia->setMediaImage($newFilename);
                $cmsMedia->setMediaImagePath($this->getParameter('public_file_folder'));
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_media_index');
        }

        return $this->render('cms/cms_media/form.html.twig', [
            'cms_media' => $cmsMedia,
            'form' => $form->createView(),
            'index_path' => 'cms_media_index',
            'label_title' => 'label.media',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param CmsMedia $cmsMedia
     * @return Response
     */
    public function delete(Request $request, CmsMedia $cmsMedia): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cmsMedia->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsMedia);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_media_index');
    }
}
