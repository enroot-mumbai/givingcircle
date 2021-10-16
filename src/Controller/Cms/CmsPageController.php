<?php

namespace App\Controller\Cms;

use App\Entity\Media\MdaMedia;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Cms\CmsPage;
use App\Form\Cms\CmsPageType;
use App\Entity\Organization\OrgCompany;
use App\Repository\Cms\CmsPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;

/**
 * @Route("/core/cms/page", name="cms_page_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsPageController extends AbstractController
{
    private $commonHelper;
    private $fileUploaderHelper;
    public function __construct(CommonHelper $commonHelper, FileUploaderHelper $fileUploaderHelper)
    {
        $this->commonHelper = $commonHelper;
        $this->fileUploaderHelper = $fileUploaderHelper;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsPageRepository $cmsPageRepository
     * @return Response
     */
    public function index(CmsPageRepository $cmsPageRepository): Response
    {
        $pages = $cmsPageRepository->findAll();
        return $this->render('cms/cms_page/index.html.twig', [
            'cms_pages' => $pages,
            'path_index' => 'cms_page_index',
            'path_add' => 'cms_page_add',
            'path_edit' => 'cms_page_edit',
            'path_banner' => 'cms_banner_index',
            'path_show' => 'cms_page_show',
            'label_title' => 'label.page',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $cmsPage = new CmsPage();
        $form = $this->createForm(CmsPageType::class, $cmsPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cmsPage->setRowId(Uuid::uuid4()->toString());
            $cmsPage->setSlugName($this->commonHelper->slugify($form->get('slugName')->getData()));
            if ($cmsPage->getParentId() == 'null' || $cmsPage->getParentId() == '') {
                $cmsPage->setParentId('0');
            }
            // Setup the content
            foreach ($form->get('cmsPageContent') as $key=>$content) {
                $cmsPageContent = $cmsPage->getCmsPageContent()[$key];
                //$cmsPageContent->setPageContent(strip_tags($content['pageContent']->getData(), ['p', 'br', 'b', 'i']));


                $mediaType = '';
                if($content['mediaType']->getData() !== null) {
                    $mediaType = $content['mediaType']->getData();
                }

                $existingPageContentFile = $cmsPageContent->getMediaFileName();
                if ($mediaType == 'image' ){
                    $pageContentFile = $content['mediaFileName']->getData();
                    $pageContentFileOld = $cmsPageContent->getMediaFileName();
                    if ($pageContentFile) {
                        $newFilename = $this->fileUploaderHelper->uploadPublicFile($pageContentFile, $content['mediaName']->getData().'_'.date("d_m_Y_h_i_s"), $pageContentFileOld);
                        $cmsPageContent->setMediaFileName($newFilename);
                        $cmsPageContent->setMediaType($content['mediaType']->getData());
                        $cmsPageContent->setMediaPosition($content['mediaPosition']->getData());
                        $cmsPageContent->setMediaName($content['mediaName']->getData());
                        $cmsPageContent->setMediaAlText($content['mediaAlText']->getData());
                        $cmsPageContent->setMediaTitle($content['mediaTitle']->getData());
                        $cmsPageContent->setPosition($content['position']->getData());
                        $cmsPageContent->setMediaFilePath($this->getParameter('public_file_folder'));
                        $cmsPageContent->setMediaPath('');
                    }
                }
                if ($mediaType == 'video') {
                    $video = $content['mediaPath']->getData();
                    if ($video) {
                        $this->fileUploaderHelper->removeFile($existingPageContentFile);
                        $cmsPageContent->setMediaFileName('');
                        $cmsPageContent->setMediaType($content['mediaType']->getData());
                        $cmsPageContent->setMediaPosition($content['mediaPosition']->getData());
                        $cmsPageContent->setMediaName($content['mediaName']->getData());
                        $cmsPageContent->setMediaAlText('');
                        $cmsPageContent->setMediaTitle('');
                        $cmsPageContent->setPosition($content['position']->getData());
                        $cmsPageContent->setMediaFilePath('');
                        $cmsPageContent->setMediaPath($video);
                    }
                }

            }
            // Upload the OG Image for SEO
            $ogImageFile = $request->files->get('cms_page')['seoContent']['ogImage'];
            if ($ogImageFile) {
                $seoContent = $cmsPage->getSeoContent();
                $newFilename = $this->fileUploaderHelper->uploadPublicFile($ogImageFile, $seoContent->getOgTitle(),null);
                $seoContent->setOgImage($newFilename);
                $seoContent->setOgImagePath($this->getParameter('public_file_folder'));
            }

            $cmsPage->setLastUpdateTime(new \DateTime());
            $cmsPage->setLastUpdateTime(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsPage);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_page_index');
        }

        return $this->render('cms/cms_page/form.html.twig', [
            'cms_page' => $cmsPage,
            'form' => $form->createView(),
            'index_path' => 'cms_page_index',
            'label_title' => 'label.page',
            'label_button' => 'label.create',
            'seo' => $cmsPage->getSeoContent(),
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param CmsPage $cmsPage
     * @return Response
     */
    public function show(CmsPage $cmsPage): Response
    {
        return $this->render('cms/cms_page/show.html.twig', [
            'data' => $cmsPage,
            'index_path' => 'cms_page_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_page_index',
            'label_title' => 'label.page',
            'path_edit' => 'cms_page_edit',
            'path_delete' => 'cms_page_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsPage $cmsPage
     * @return Response
     */
    public function edit(Request $request, CmsPage $cmsPage): Response
    {
        $existingOgImageFile = $cmsPage->getSeoContent()->getOgImage();
        $form = $this->createForm(CmsPageType::class, $cmsPage);
        $form->handleRequest($request);

        // Get the existing Data
        $originalContent = new ArrayCollection();
        foreach ($cmsPage->getCmsPageContent() as $content)
        {
            $originalContent->add($content);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $cmsPage->setSlugName($this->commonHelper->slugify($form->get('slugName')->getData()));
            if ($cmsPage->getParentId() == 'null' || $cmsPage->getParentId() == '') {
                $cmsPage->setParentId('0');
            }
            foreach ($originalContent as $content)
            {
                if($cmsPage->getCmsPageContent()->contains($content) == false) {
                    $this->getDoctrine()->getManager()->remove($content);
                }
            }
            // Setup the content
            foreach ($form->get('cmsPageContent') as $key=>$content) {
                $cmsPageContent = $cmsPage->getCmsPageContent()[$key];
//                $cmsPageContent->setPageContent(strip_tags($content['pageContent']->getData(), ['p', 'br', 'b', 'i']));

                $mediaType = '';
                $mediaType = $cmsPageContent->getMediaType();
                $existingPageContentFile = $cmsPageContent->getMediaFileName();
                if(!empty($request->get('cms_page')['cmsPageContent'][$key]['removeContentImage']))
                {
                    // Remove the image from the system
                    $this->fileUploaderHelper->removeFile($cmsPageContent->getMediaFileName());
                    $cmsPageContent->setMediaType('');
                    $cmsPageContent->setMediaPosition('');
                    $cmsPageContent->setMediaName('');
                    $cmsPageContent->setMediaAlText('');
                    $cmsPageContent->setMediaTitle('');
                    $cmsPageContent->setMediaFilePath('');
                } else {

                    if ($mediaType == 'image' ){
                        $pageContentFile = $content['mediaFileName']->getData();
                        $pageContentFileOld = $cmsPageContent->getMediaFileName();
                        if ($pageContentFile) {
                            $newFilename = $this->fileUploaderHelper->uploadPublicFile($pageContentFile, $content['mediaName']->getData().'_'.date("d_m_Y_h_i_s"), $pageContentFileOld);
                            $cmsPageContent->setMediaFileName($newFilename);
                            $cmsPageContent->setMediaType($content['mediaType']->getData());
                            $cmsPageContent->setMediaPosition($content['mediaPosition']->getData());
                            $cmsPageContent->setMediaName($content['mediaName']->getData());
                            $cmsPageContent->setMediaAlText($content['mediaAlText']->getData());
                            $cmsPageContent->setMediaTitle($content['mediaTitle']->getData());
                            $cmsPageContent->setPosition($content['position']->getData());
                            $cmsPageContent->setMediaFilePath($this->getParameter('public_file_folder'));
                            $cmsPageContent->setMediaPath('');
                        }
                    }

                }
                if ($mediaType == 'video') {
                    $video = $content['mediaPath']->getData();
                    if ($video) {
                        $this->fileUploaderHelper->removeFile($existingPageContentFile);
                        $cmsPageContent->setMediaFileName('');
                        $cmsPageContent->setMediaType($content['mediaType']->getData());
                        $cmsPageContent->setMediaPosition($content['mediaPosition']->getData());
                        $cmsPageContent->setMediaName($content['mediaName']->getData());
                        $cmsPageContent->setMediaAlText('');
                        $cmsPageContent->setMediaTitle('');
                        $cmsPageContent->setPosition($content['position']->getData());
                        $cmsPageContent->setMediaFilePath('');
                        $cmsPageContent->setMediaPath($video);
                    }
                }

            }
//            // Upload the OG Image for SEO
            $seoContent = $cmsPage->getSeoContent();
            $ogImageFile = $request->files->get('cms_page')['seoContent']['ogImage'];
            if ($ogImageFile) {
                if ($existingOgImageFile != '')
                {
                    $newFilename = $this->fileUploaderHelper->uploadPublicFile($ogImageFile, $seoContent->getOgTitle(),$existingOgImageFile);
                } else {
                    $newFilename = $this->fileUploaderHelper->uploadPublicFile($ogImageFile, $seoContent->getOgTitle(),null);
                }
                $seoContent->setOgImage($newFilename);
                $seoContent->setOgImagePath($this->getParameter('public_file_folder'));
            }

            $cmsPage->setLastUpdateTime(new \DateTime());

            $entityManager->persist($cmsPage);
            $entityManager->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_page_index');
        }

        return $this->render('cms/cms_page/form.html.twig', [
            'cms_page' => $cmsPage,
            'form' => $form->createView(),
            'index_path' => 'cms_page_index',
            'label_title' => 'label.page',
            'label_button' => 'label.update',
            'seo' => $cmsPage->getSeoContent(),
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/query", name="query", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function query(Request $request): Response
    {
        if(isset($_POST)) {

            $query = trim($_POST['querytxt']);

            $conn = $this->getDoctrine()->getConnection();
            $stmt = $conn->prepare($query);
            $result = $stmt->execute();

            dd($result);
        }
//        $cmsPage = new CmsPage();

        /*return $this->render('cms/cms_page/query.html.twig', [
            'cmsPage'=> $cmsPage
        ]);*/
    }

}
