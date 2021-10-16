<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsArticleContent;
use App\Service\ArticleUpload;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Cms\CmsArticle;
use App\Form\Cms\CmsArticleType;
use App\Repository\Cms\CmsArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;

/**
 * @Route("/core/cms/article", name="cms_article_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsArticleController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsArticleRepository $cmsArticleRepository
     * @return Response
     */
    public function index(CmsArticleRepository $cmsArticleRepository): Response
    {
        $articles = $cmsArticleRepository->findAll();
        return $this->render('cms/cms_article/index.html.twig', [
            'cms_articles' => $articles,
            'path_index' => 'cms_article_index',
            'path_add' => 'cms_article_add',
            'path_edit' => 'cms_article_edit',
            'path_comment' => 'cms_article_comment_index',
            'path_show' => 'cms_article_show',
            'label_title' => 'label.article',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param CommonHelper $commonHelper
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     */
    public function new(Request $request, CommonHelper $commonHelper, FileUploaderHelper $fileUploaderHelper): Response
    {
        $cmsArticle = new CmsArticle();
        $option = array('image_required' => false);
        $form = $this->createForm(CmsArticleType::class, $cmsArticle, $option);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $cmsArticle->setRowId(Uuid::uuid4()->toString());
            //$cmsArticle->setArticleCreateDateTime(new DateTime());
            $cmsArticle->setArticleCreatedBy($this->getUser());

            // Check the article category in case it is Change Maker we need to define a different slug name
            $articleCategory = $form->get('mstArticleCategory')->getData()->getId();

            if ($articleCategory == 2) {
                $interestName = $form->get('mstAreaInterest')->getData()->getAreaInterest();
                $articleFor = $form->get('articleFor')->getData();
                $locationName = $form->get('locationName')->getData();
                $city = $form->get('mstCity')->getData()->getCity();
                $slugName = $articleFor . ' ' . $locationName . ' ' . $city . ' ' . $interestName;
                $cmsArticle->setArticleSlugName($commonHelper->slugify($slugName));
                $cmsArticle->setCityName($city);
            } else {
                $cmsArticle->setArticleSlugName($commonHelper->slugify($form->get('articleTitle')->getData()));
            }
            // Set up Intro media
            $introMediaType = $form['introMediaType']->getData();

            // If Media Type is image
            if ($introMediaType == 'image') {
                // Upload the intro image for Article
                $articleIntroFile = $form['articleIntroImage']->getData();
                if ($articleIntroFile) {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroFile, $form['articleIntroImageSetName']->getData().'_'.date("d_m_Y_h_i_s"));
                    $cmsArticle->setArticleIntroImage($newFilename);
                    $cmsArticle->setArticleIntroImageSetName($form->get('articleIntroImageSetName')->getData());
                    $cmsArticle->setArticleIntroImagePath($this->getParameter('public_file_folder'));
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
                $cmsArticleContent->setMediaType('image');
                $articleContentFile = $content['articleContentImage']->getData();
                if ($articleContentFile) {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($articleContentFile, $content['articleContentImageSetName']->getData().'_'.date("d_m_Y_h_i_s"));
                    $cmsArticleContent->setArticleContentImage($newFilename);
                    $cmsArticleContent->setArticleContentImageSetName($content['articleContentImageSetName']->getData());
                    $cmsArticleContent->setArticleContentImageAlt($content['articleContentImageAlt']->getData());
                    $cmsArticleContent->setArticleContentImageTitle($content['articleContentImageTitle']->getData());
                    $cmsArticleContent->setArticleContentImagePath($this->getParameter('public_file_folder'));
                }
            }

            $entityManager->persist($cmsArticle);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_article_index');
        }
        return $this->render('cms/cms_article/form.html.twig', [
                'cms_article' => $cmsArticle,
                'form' => $form->createView(),
                'index_path' => 'cms_article_index',
                'label_title' => 'label.article',
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
        return $this->render('cms/cms_article/show.html.twig', [
            'data' => $cmsArticle,
            'index_path' => 'cms_article_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_article_index',
            'path_edit' => 'cms_article_edit',
            'path_delete' => 'cms_article_delete',
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
        $option = array('image_required' => false);
        $form = $this->createForm(CmsArticleType::class, $cmsArticle, $option);
        $form->handleRequest($request);

        // Get the existing Data
        $originalContent = new ArrayCollection();
        foreach ($cmsArticle->getCmsArticleContent() as $content)
        {
            $originalContent->add($content);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach($originalContent as $cntnt){
                if($cmsArticle->getCmsArticleContent()->contains($cntnt)==false){
                    $this->getDoctrine()->getManager()->remove($cntnt);
                }
            }
            // Check the article category in case it is Change Maker we need to define a different slug name
            $articleCategory = $form->get('mstArticleCategory')->getData()->getId();

            if ($articleCategory == 2) {
                $interestName = $form->get('mstAreaInterest')->getData()->getAreaInterest();
                $articleFor = $form->get('articleFor')->getData();
                $locationName = $form->get('locationName')->getData();
                $city = $form->get('mstCity')->getData()->getCity();
                $slugName = $articleFor . ' ' . $locationName . ' ' . $city . ' ' . $interestName;
                $cmsArticle->setCityName($city);
                $cmsArticle->setArticleSlugName($commonHelper->slugify($slugName));
            } else {
                $cmsArticle->setArticleSlugName($commonHelper->slugify($form->get('articleTitle')->getData()));
            }

            // Set up Intro media
            $introMediaType = $form['introMediaType']->getData();

            // If Media Type is image
            if ($introMediaType == 'image') {
                // Upload the intro image for Article
                $articleIntroFile = $form['articleIntroImage']->getData();
                if ($articleIntroFile) {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroFile, $form['articleIntroImageSetName']->getData().'_'.date("d_m_Y_h_i_s"));
                    $cmsArticle->setArticleIntroImage($newFilename);
                    $cmsArticle->setArticleIntroImageSetName($form->get('articleIntroImageSetName')->getData());
                    $cmsArticle->setArticleIntroImagePath($this->getParameter('public_file_folder'));
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
                $cmsArticleContent->setMediaType('image');
                $articleContentFile = $content['articleContentImage']->getData();
                if ($articleContentFile) {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($articleContentFile, $content['articleContentImageSetName']->getData().'_'.date("d_m_Y_h_i_s"));
                    $cmsArticleContent->setArticleContentImage($newFilename);
                    $cmsArticleContent->setArticleContentImageSetName($content['articleContentImageSetName']->getData());
                    $cmsArticleContent->setArticleContentImageAlt($content['articleContentImageAlt']->getData());
                    $cmsArticleContent->setArticleContentImageTitle($content['articleContentImageTitle']->getData());
                    $cmsArticleContent->setArticleContentImagePath($this->getParameter('public_file_folder'));
                }
            }
            $cmsArticle->setArticleUpdateDateTime(new DateTime());
            $cmsArticle->setArticleUpdatedBy($this->getUser());
            $entityManager->persist($cmsArticle);
            $entityManager->flush();

            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_article_index');
        }

        return $this->render('cms/cms_article/form.html.twig', [
            'cms_article' => $cmsArticle,
            'form' => $form->createView(),
            'index_path' => 'cms_article_index',
            'label_title' => 'label.article',
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
