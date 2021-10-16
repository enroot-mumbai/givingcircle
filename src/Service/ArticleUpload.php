<?php


namespace App\Service;


use App\Entity\Cms\CmsArticleContent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class ArticleUpload
{
    private $em;
    private $fileUploaderHelper;
    private $commonHelper;

    public function __construct(EntityManagerInterface $em, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper)
    {
        $this->em = $em;
        $this->fileUploaderHelper = $fileUploaderHelper;
        $this->commonHelper = $commonHelper;
    }

    public function article($form, $cmsArticle, $uploadDirectory, $user, $changeType)
    {
        // Check the article category in case it is Change Maker we need to define a different slug name
        $articleCategory = $form->get('mstArticleCategory')->getData()->getId();

        // If it is Change Maker
        if ($articleCategory == 2) {
            $interestName = $form->get('mstAreaInterest')->getData()->getAreaInterest();
            $articleFor = $form->get('articleFor')->getData();
            $locationName = $form->get('locationName')->getData();
            $city = $form->get('mstCity')->getData()->getCity();
            $slugName = $articleFor . ' ' . $locationName . ' ' . $city . ' ' . $interestName;
            $cmsArticle->setArticleSlugName($this->commonHelper->slugify($slugName));
            $cmsArticle->setCityName($city);
        } else {
            $cmsArticle->setArticleSlugName($this->commonHelper->slugify($form->get('articleTitle')->getData()));

        }
        // Set up Intro media
        $introMediaType = $form['introMediaType']->getData();
        // If Media Type is image
        if ($introMediaType == 'image') {
            // Upload the intro image for Article
            $articleIntroFile = $form['articleIntroImage']->getData();
            if ($articleIntroFile) {
                $newFilename = $this->fileUploaderHelper->uploadPublicFile($articleIntroFile, $form['articleIntroImageSetName']->getData());
                $cmsArticle->setArticleIntroImage($newFilename);
                $cmsArticle->setArticleIntroImageSetName($form->get('articleIntroImageSetName')->getData());
                $cmsArticle->setArticleIntroImagePath($uploadDirectory);
            }

        }
        // If media type is video
        if ($introMediaType == 'video') {
            $cmsArticle->setArticleIntroVideo($form->get('articleIntroVideo')->getData());
            $cmsArticle->setArticleIntroVideoPath($form->get('articleIntroVideoPath')->getData());
        }
        // Get Content
        if (!empty($form->get('cmsArticleContent'))) {

            foreach ($form->get('cmsArticleContent') as $content) {
//                $cmsArticleContent->setCmsArticle($cmsArticle);
//                $cmsArticleContent->setArticleContent($content['articleContent']->getData());
//                $cmsArticleContent->setMediaType($content['mediaType']->getData());
                // Get Content media type
       //         $contentMediaType = $content['mediaType']->getData();
                // If Content media type is image
//                if ($contentMediaType == 'image') {
                    // Upload the intro image for Article
                    $articleContentFile = $content['articleContentImage']->getData();
                    if ($articleContentFile) {
                        $newFilename = $this->fileUploaderHelper->uploadPublicFile($articleContentFile, $content['articleContentImageSetName']->getData());
                        $cmsArticle->setCmsArticleContent()->setArticleContentImage($newFilename);
//                        $cmsArticleContent->setArticleContentImageSetName($content['articleContentImageSetName']->getData());
//                        $cmsArticleContent->setArticleContentImageAlt($content['articleContentImageAlt']->getData());
//                        $cmsArticleContent->setArticleContentImageTitle($content['articleContentImageTitle']->getData());
//                        $cmsArticleContent->setArticleContentImagePath($uploadDirectory);
                    }
//                }
//                // If Content media type is video
//                if ($contentMediaType == 'video') {
//                    $cmsArticleContent->setArticleContentVideo($content['articleContentVideo']->getData());
//                    $cmsArticleContent->setArticleContentVideoPath($content['articleContentVideoPath']->getData());
//                }
//                $this->em->persist($cmsArticleContent);
            }
        }

        if ($changeType == 'add') {
            $cmsArticle->setRowId(Uuid::uuid4()->toString());

            $cmsArticle->setArticleCreateDateTime(new DateTime());
            $cmsArticle->setArticleCreatedBy($user);
        }

        if ($changeType == 'edit') {
            $cmsArticle->setArticleUpdateDateTime(new DateTime());
            $cmsArticle->setArticleUpdatedBy($user);
        }

        $this->em->persist($cmsArticle);
        $this->em->flush();

    }
}

