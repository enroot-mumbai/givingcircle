<?php

namespace App\Controller\Cms;

use App\Form\Cms\CmsArticleChangeMakerType;
use App\Service\ArticleUpload;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Cms\CmsArticle;
use App\Repository\Cms\CmsArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;

/**
 * @Route("/core/cms/changemaker", name="cms_changemaker_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsArticleChangeMakerController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsArticleRepository $cmsArticleRepository
     * @return Response
     */
    public function index(CmsArticleRepository $cmsArticleRepository): Response
    {
        return $this->render('cms/cms_article_change_maker/index.html.twig', [
            'cms_articles' => $cmsArticleRepository->findBy(['mstArticleCategory' => 2]),
            'path_index' => 'cms_changemaker_index',
            'path_add' => 'cms_changemaker_add',
            'path_edit' => 'cms_changemaker_edit',
            'path_comment' => 'cms_changemaker_comment_index',
            'path_show' => 'cms_changemaker_show',
            'label_title' => 'label.changemaker',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param CmsArticleRepository $cmsArticleRepository
     * @param CommonHelper $commonHelper
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, CmsArticleRepository $cmsArticleRepository, CommonHelper $commonHelper, FileUploaderHelper $fileUploaderHelper): Response
    {
        $cmsArticle = new CmsArticle();
        $option = array('image_required' => false);
        $sequenceNo = $cmsArticleRepository->findOneBySeqNo(2);
        $cmsArticle->setSequenceNo(($sequenceNo[1] + 1));
        $form = $this->createForm(CmsArticleChangeMakerType::class, $cmsArticle, $option);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $cmsArticle->setRowId(Uuid::uuid4()->toString());
            $cmsArticle->setArticleCreateDateTime(new DateTime('now'));
            $cmsArticle->setArticleCreatedBy($this->getUser());

            // Upload the OG Image for SEO
            $ogImageFile = $form['ogImage']->getData();
            if ($ogImageFile) {
                $newFilename = $fileUploaderHelper->uploadPublicFile($ogImageFile, $form['ogImage']->getData().'_'.date("d_m_Y_h_i_s"),null);
                $cmsArticle->setOgImage($newFilename);
                $cmsArticle->setOgImagePath($this->getParameter('public_file_folder'));
            }

            $city = $form->get('mstCity')->getData()->getCity();

            $cmsArticle->setArticleSlugName($commonHelper->slugify($form->get('articleFor')->getData()));
            $cmsArticle->setCityName($city);
            // Set up Intro media
            $introMediaType = $form['introMediaType']->getData();

            // If Media Type is image
            if ($introMediaType == 'image') {
                // Upload the intro image for Article
                $articleIntroDesktopFile = $form['articleIntroDesktopImage']->getData();
                $articleIntroTabletFile = $form['articleIntroDesktopImage']->getData();
                $articleIntroMobileFile = $form['articleIntroDesktopImage']->getData();
                $articleIntroTabletFileSetName = $form['articleIntroImageSetName']->getData().'_'.date("d_m_Y_h_i_s").'-tablet';
                $articleIntroMobileFileSetName = $form['articleIntroImageSetName']->getData().'_'.date("d_m_Y_h_i_s").'-mobile';
                if ($articleIntroDesktopFile) {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroDesktopFile, $form['articleIntroImageSetName']->getData().'_'.date("d_m_Y_h_i_s"),null);
                    $cmsArticle->setArticleIntroDesktopImage($newFilename);
                    $cmsArticle->setArticleIntroImageSetName($form->get('articleIntroImageSetName')->getData());
                    $cmsArticle->setArticleIntroDesktopImagePath($this->getParameter('public_file_folder'));
                }
                if ($articleIntroTabletFile) {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroTabletFile, $articleIntroTabletFileSetName,null);
                    $cmsArticle->setArticleIntroTabletImage($newFilename);
                    $cmsArticle->setArticleIntroTabletImagePath($this->getParameter('public_file_folder'));
                }
                if ($articleIntroMobileFile) {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroMobileFile, $articleIntroMobileFileSetName,null);
                    $cmsArticle->setArticleIntroMobileImage($newFilename);
                    $cmsArticle->setArticleIntroMobileImagePath($this->getParameter('public_file_folder'));
                }
            }

            // If media type is video
            if ($introMediaType == 'video') {
                $cmsArticle->setArticleIntroVideo($form->get('articleIntroVideo')->getData());
                $cmsArticle->setArticleIntroVideoPath($form->get('articleIntroVideoPath')->getData());
            }

            foreach ($form->get('cmsArticleContent') as $key=>$content) {
                $cmsArticleContent = $cmsArticle->getCmsArticleContent()[$key];
                $cmsArticleContent->setArticleContent($content['articleContent']->getData());
//                $cmsArticleContent->setMediaType('image');
                $mediaType = $cmsArticleContent->getMediaType();
                if ($mediaType == 'image' ){
                    $articleContentDesktopFile = $content['articleContentDesktopImage']->getData();
                    $articleContentTabletFile = $content['articleContentTabletImage']->getData();
                    $articleContentMobileFile = $content['articleContentMobileImage']->getData();
                    $articleContentTabletFileSetName = $content['articleContentImageSetName']->getData().'_'.date("d_m_Y_h_i_s").'-tablet';
                    $articleContentMobileFileSetName = $content['articleContentImageSetName']->getData().'_'.date("d_m_Y_h_i_s").'-mobile';
                    if ($articleContentDesktopFile) {
                        $newFilename = $fileUploaderHelper->uploadPublicFile($articleContentDesktopFile, $content['articleContentImageSetName']->getData().'_'.date("d_m_Y_h_i_s"),null);
                        $cmsArticleContent->setArticleContentDesktopImage($newFilename);
                        $cmsArticleContent->setArticleContentImageSetName($content['articleContentImageSetName']->getData());
                        $cmsArticleContent->setArticleContentImageAlt($content['articleContentImageAlt']->getData());
                        $cmsArticleContent->setArticleContentImageTitle($content['articleContentImageTitle']->getData());
                        $cmsArticleContent->setArticleContentDesktopImagePath($this->getParameter('public_file_folder'));
                    }
                    if ($articleContentTabletFile) {
                        $newFilename = $fileUploaderHelper->uploadPublicFile($articleContentTabletFile, $articleContentTabletFileSetName,null);
                        $cmsArticleContent->setArticleContentTabletImage($newFilename);
                        $cmsArticleContent->setArticleContentTabletImagePath($this->getParameter('public_file_folder'));
                    }
                    if ($articleContentMobileFile) {
                        $newFilename = $fileUploaderHelper->uploadPublicFile($articleContentMobileFile, $articleContentMobileFileSetName,null);
                        $cmsArticleContent->setArticleContentMobileImage($newFilename);
                        $cmsArticleContent->setArticleContentMobileImagePath($this->getParameter('public_file_folder'));
                    }

                }
                if ($mediaType == 'video' ){
                    $video = $content['articleContentVideo']->getData();
                    if ($video) {
                        $cmsArticleContent->setArticleContentVideo($content['articleContentVideo']->getData());
                        $cmsArticleContent->setArticleContentVideoPath($content['articleContentVideoPath']->getData());
                    }
                }
            }

            $entityManager->persist($cmsArticle);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_changemaker_index');
        }
        return $this->render('cms/cms_article_change_maker/form.html.twig', [
                'cms_article' => $cmsArticle,
                'form' => $form->createView(),
                'index_path' => 'cms_changemaker_index',
                'label_title' => 'label.changemaker',
                'label_button' => 'label.create',
                'mode' => 'add'
        ]);

    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param CmsArticle $cmsArticle
     * @return Response
     */
    public function show(CmsArticle $cmsArticle): Response
    {
        return $this->render('cms/cms_article_blog/show.html.twig', [
            'data' => $cmsArticle,
            'index_path' => 'cms_changemaker_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_changemaker_index',
            'path_edit' => 'cms_changemaker_edit',
            'path_delete' => 'cms_changemaker_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsArticle $cmsArticle
     * @param ArticleUpload $articleUpload
     * @param CommonHelper $commonHelper
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     */
    public function edit(Request $request, CmsArticle $cmsArticle, ArticleUpload $articleUpload, CommonHelper $commonHelper, FileUploaderHelper $fileUploaderHelper): Response
    {
        $existingOgImageFile = $cmsArticle->getOgImage();
        $option = array('image_required' => false);
        $form = $this->createForm(CmsArticleChangeMakerType::class, $cmsArticle, $option);
        $form->handleRequest($request);

        // Get the existing Data
        $originalContent = new ArrayCollection();
        foreach ($cmsArticle->getCmsArticleContent() as $content)
        {
            $originalContent->add($content);
        }

        if ($form->isSubmitted() && $form->isValid()) {
//			dd($_POST);
            $entityManager = $this->getDoctrine()->getManager();
            foreach($originalContent as $cntnt){
                if($cmsArticle->getCmsArticleContent()->contains($cntnt)==false){
                    $this->getDoctrine()->getManager()->remove($cntnt);
                }
            }

            // Upload the OG Image for SEO
            $ogImageFile = $form['ogImage']->getData();
            if ($ogImageFile) {
                if ($existingOgImageFile != '')
                {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($ogImageFile, $form['ogImage']->getData().'_'.date("d_m_Y_h_i_s"),$existingOgImageFile);
                } else {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($ogImageFile, $form['ogImage']->getData().'_'.date("d_m_Y_h_i_s"),null);
                }

                $cmsArticle->setOgImage($newFilename);
                $cmsArticle->setOgImagePath($this->getParameter('public_file_folder'));
            }
            $city = $form->get('mstCity')->getData()->getCity();
            $cmsArticle->setArticleSlugName($commonHelper->slugify($form->get('articleFor')->getData()));
            $cmsArticle->setCityName($city);

            // Set up Intro media
            $introMediaType = $form['introMediaType']->getData();
            if(!empty($_POST['cms_article_blog']['removeIntroImage'])) {
                // Remove the image from the system
                $fileUploaderHelper->removeFile($cmsArticle->getArticleIntroDesktopImage());
                $fileUploaderHelper->removeFile($cmsArticle->getArticleIntroTabletImage());
                $fileUploaderHelper->removeFile($cmsArticle->getArticleIntroMobileImage());
                $cmsArticle->setIntroMediaType('');
                $cmsArticle->setArticleIntroDesktopImage('');
                $cmsArticle->setArticleIntroTabletImage('');
                $cmsArticle->setArticleIntroMobileImage('');
                $cmsArticle->setArticleIntroImageSetName('');
                $cmsArticle->setArticleIntroImageAlt('');
                $cmsArticle->setArticleIntroImageTitle('');
                $cmsArticle->setArticleIntroDesktopImagePath('');
                $cmsArticle->setArticleIntroTabletImagePath('');
                $cmsArticle->setArticleIntroTabletImagePath('');
            } else {
                // If Media Type is image
                if ($introMediaType == 'image') {
                    // Upload the intro image for Article
                    $articleIntroDesktopFile = $form['articleIntroDesktopImage']->getData();
                    $articleIntroTabletFile = $form['articleIntroDesktopImage']->getData();
                    $articleIntroMobileFile = $form['articleIntroDesktopImage']->getData();
                    $articleIntroTabletFileSetName = $form['articleIntroImageSetName']->getData().'_'.date("d_m_Y_h_i_s").'-tablet';
                    $articleIntroMobileFileSetName = $form['articleIntroImageSetName']->getData().'_'.date("d_m_Y_h_i_s").'-mobile';
                    $articleIntroFileOldDesktop = $cmsArticle->getArticleIntroDesktopImage();
                    $articleIntroFileOldTablet = $cmsArticle->getArticleIntroTabletImage();
                    $articleIntroFileOldMobile = $cmsArticle->getArticleIntroMobileImage();
                    if ($articleIntroDesktopFile) {
                        $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroDesktopFile, $form['articleIntroImageSetName']->getData().'_'.date("d_m_Y_h_i_s"), $articleIntroFileOldDesktop);
                        $cmsArticle->setArticleIntroDesktopImage($newFilename);
                        $cmsArticle->setArticleIntroImageSetName($form->get('articleIntroImageSetName')->getData());
                        $cmsArticle->setArticleIntroDesktopImagePath($this->getParameter('public_file_folder'));
                    }
                    if ($articleIntroTabletFile) {
                        $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroTabletFile, $articleIntroTabletFileSetName, $articleIntroFileOldTablet);
                        $cmsArticle->setArticleIntroTabletImage($newFilename);
                        $cmsArticle->setArticleIntroTabletImagePath($this->getParameter('public_file_folder'));
                    }
                    if ($articleIntroMobileFile) {
                        $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroMobileFile, $articleIntroMobileFileSetName, $articleIntroFileOldMobile);
                        $cmsArticle->setArticleIntroMobileImage($newFilename);
                        $cmsArticle->setArticleIntroMobileImagePath($this->getParameter('public_file_folder'));
                    }
                }
            }

            // If media type is video
            if ($introMediaType == 'video') {
                $cmsArticle->setArticleIntroVideo($form->get('articleIntroVideo')->getData());
                $cmsArticle->setArticleIntroVideoPath($form->get('articleIntroVideoPath')->getData());
            }


            foreach ($form->get('cmsArticleContent') as $key=>$content) {

                $cmsArticleContent = $cmsArticle->getCmsArticleContent()[$key];
                $cmsArticleContent->setArticleContent($content['articleContent']->getData());
                $mediaType = $cmsArticleContent->getMediaType();
                if(!empty($_POST['cms_article_blog']['cmsArticleContent'][$key]['removeContentImage']))
                {
                    // Remove the image from the system
                    $fileUploaderHelper->removeFile($cmsArticleContent->getArticleContentDesktopImage());
                    $fileUploaderHelper->removeFile($cmsArticleContent->getArticleContentTabletImage());
                    $fileUploaderHelper->removeFile($cmsArticleContent->getArticleContentMobileImage());
                    $cmsArticleContent->setMediaType('');
                    $cmsArticleContent->setArticleContentDesktopImage('');
                    $cmsArticleContent->setArticleContentTabletImage('');
                    $cmsArticleContent->setArticleContentMobileImage('');
                    $cmsArticleContent->setArticleContentImageSetName('');
                    $cmsArticleContent->setArticleContentImageAlt('');
                    $cmsArticleContent->setArticleContentImageTitle('');
                    $cmsArticleContent->setArticleContentDesktopImagePath('');
                    $cmsArticleContent->setArticleContentTabletImagePath('');
                    $cmsArticleContent->setArticleContentMobileImagePath('');
                } else {

                    if ($mediaType == 'image' ){
                        $articleContentDesktopFile = $content['articleContentDesktopImage']->getData();
                        $articleContentTabletFile = $content['articleContentTabletImage']->getData();
                        $articleContentMobileFile = $content['articleContentMobileImage']->getData();
                        $articleContentTabletFileSetName = $content['articleContentImageSetName']->getData().'_'.date("d_m_Y_h_i_s").'-tablet';
                        $articleContentMobileFileSetName = $content['articleContentImageSetName']->getData().'_'.date("d_m_Y_h_i_s").'-mobile';
                        $existingArticleContentDesktopFile = $cmsArticleContent->getArticleContentDesktopImage();
                        $existingArticleContentTabletFile = $cmsArticleContent->getArticleContentTabletImage();
                        $existingArticleContentMobileFile = $cmsArticleContent->getArticleContentMobileImage();
                        if ($articleContentDesktopFile) {
                            $newFilename = $fileUploaderHelper->uploadPublicFile($articleContentDesktopFile, $content['articleContentImageSetName']->getData().'_'.date("d_m_Y_h_i_s"),$existingArticleContentDesktopFile );
                            $cmsArticleContent->setArticleContentDesktopImage($newFilename);
                            $cmsArticleContent->setArticleContentImageSetName($content['articleContentImageSetName']->getData());
                            $cmsArticleContent->setArticleContentImageAlt($content['articleContentImageAlt']->getData());
                            $cmsArticleContent->setArticleContentImageTitle($content['articleContentImageTitle']->getData());
                            $cmsArticleContent->setArticleContentDesktopImagePath($this->getParameter('public_file_folder'));
                            $cmsArticleContent->setArticleContentVideo('');
                            $cmsArticleContent->setArticleContentVideoPath('');
                        }
                        if ($articleContentTabletFile) {
                            $newFilename = $fileUploaderHelper->uploadPublicFile($articleContentDesktopFile, $articleContentTabletFileSetName, $existingArticleContentTabletFile );
                            $cmsArticleContent->setArticleContentTabletImage($newFilename);
                            $cmsArticleContent->setArticleContentTabletImagePath($this->getParameter('public_file_folder'));
                        }
                        if ($articleContentMobileFile) {
                            $newFilename = $fileUploaderHelper->uploadPublicFile($articleContentMobileFile, $articleContentMobileFileSetName, $existingArticleContentMobileFile );
                            $cmsArticleContent->setArticleContentMobileImage($newFilename);
                            $cmsArticleContent->setArticleContentMobileImagePath($this->getParameter('public_file_folder'));
                        }
                    }

                }
                if ($mediaType == 'video') {
                    $video = $content['articleContentVideo']->getData();
                    if ($video) {
                        $fileUploaderHelper->removeFile($existingArticleContentDesktopFile);
                        $fileUploaderHelper->removeFile($existingArticleContentTabletFile);
                        $fileUploaderHelper->removeFile($existingArticleContentMobileFile);
                        $cmsArticleContent->setArticleContentDesktopImage('');
                        $cmsArticleContent->setArticleContentTabletImage('');
                        $cmsArticleContent->setArticleContentMobileImage('');
                        $cmsArticleContent->setArticleContentImageSetName('');
                        $cmsArticleContent->setArticleContentImageAlt('');
                        $cmsArticleContent->setArticleContentImageTitle('');
                        $cmsArticleContent->setArticleContentDesktopImagePath('');
                        $cmsArticleContent->setArticleContentTabletImagePath('');
                        $cmsArticleContent->setArticleContentMobileImagePath('');
                        $cmsArticleContent->setArticleContentVideo($content['articleContentVideo']->getData());
                        $cmsArticleContent->setArticleContentVideoPath($content['articleContentVideoPath']->getData());
                    }
                }

            }
            $cmsArticle->setArticleUpdateDateTime(new DateTime());
            $cmsArticle->setArticleUpdatedBy($this->getUser());
            $entityManager->persist($cmsArticle);
            $entityManager->flush();

            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_changemaker_index');
        }

        return $this->render('cms/cms_article_change_maker/form.html.twig', [
            'cms_article' => $cmsArticle,
            'form' => $form->createView(),
            'index_path' => 'cms_changemaker_index',
            'label_title' => 'label.changemaker',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param CmsArticle $cmsArticle
     * @return Response
     */
    public function delete(Request $request, CmsArticle $cmsArticle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cmsArticle->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsArticle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_article_index');
    }
}
