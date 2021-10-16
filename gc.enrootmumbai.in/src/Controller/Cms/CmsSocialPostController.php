<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsSocialPost;
use App\Form\Cms\CmsSocialPostEditType;
use App\Form\Cms\CmsSocialPostType;
use App\Repository\Cms\CmsSocialPostRepository;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use App\Service\Social;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("cms/social_post", name="cms_social_post_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsSocialPostController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsSocialPostRepository $cmsSocialPostRepository
     * @return Response
     */
    public function index(CmsSocialPostRepository $cmsSocialPostRepository): Response
    {
        $posts = $cmsSocialPostRepository->findAll();
        return $this->render('cms/cms_social_post/index.html.twig', [
            'cms_social_posts' => $posts,
            'path_index' => 'cms_social_post_index',
            'path_add' => 'cms_social_post_add',
            'path_edit' => 'cms_social_post_edit',
            'path_show' => 'cms_social_post_show',
            'label_title' => 'label.social',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param Social $social
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, Social $social, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $cmsSocialPost = new CmsSocialPost();
        $form = $this->createForm(CmsSocialPostType::class, $cmsSocialPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cmsSocialPost->setIsPublish(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsSocialPost);
            $entityManager->flush();
            if ($form['postPicture']->getData()) {
                $image = $form['postPicture']->getData();
                $newFilename = $fileUploaderHelper->uploadPublicFile($image, $commonHelper->slugify($form['postPicture']->getData()), $existingImage = null);
                $cmsSocialPost->setPostPicture($newFilename);
                $cmsSocialPost->setPostPicturePath($this->getParameter('public_file_folder'));
            } else {
                $image = null;
            }
            $cmsSocialPost->setPostStatus('created');
            $social->createPost($cmsSocialPost, $image);

            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_social_post_index');
        }
        return $this->render('cms/cms_social_post/form.html.twig', [
            'cms_social_post' => $cmsSocialPost,
            'form' => $form->createView(),
            'index_path' => 'cms_social_post_index',
            'label_title' => 'label.social_post',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param CmsSocialPost $cmsSocialPost
     * @return Response
     */
    public function show(CmsSocialPost $cmsSocialPost): Response
    {
        return $this->render('cms/cms_social_post/show.html.twig', [
            'data' => $cmsSocialPost,
            'index_path' => 'cms_social_post_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_social_post_index',
            'path_edit' => 'cms_social_post_edit',
            'path_delete' => 'cms_social_post_delete',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsSocialPost $cmsSocialPost
     * @param Social $social
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, CmsSocialPost $cmsSocialPost, Social $social, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $existingImage = $cmsSocialPost->getPostPicture();

        if ($cmsSocialPost->getPostStatus() == 'deleted')
        {
            echo "Post is deleted";
            exit();
        }

        $form = $this->createForm(CmsSocialPostEditType::class, $cmsSocialPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//           if ($form['postPicture']->getData()) {
//                $image = $form['postPicture']->getData();
//                // If there is already a media remove it
//                if($existingImage != '')
//                {
//                    $newFilename = $fileUploaderHelper->uploadPublicFile($image, $commonHelper->slugify($form['postPicture']->getData()), $existingImage);
//                } else {
//                    $newFilename = $fileUploaderHelper->uploadPublicFile($image, $commonHelper->slugify($form['postPicture']->getData()), $existingImage = null);
//                }
//                $cmsSocialPost->setPostPicture($newFilename);
//            } else {
//                $image = null;
//            }
            $image = null;
            $response = $social->updateFacebookPost($cmsSocialPost, $image);
            if ($response['success'] == true) {
                $cmsSocialPost->setPostStatus('updated');
                $this->addFlash('success', 'Post updated successfully.');
            } else {
                $this->addFlash('success', 'There was an issue regarding updation of post.');
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('cms_social_post_index');
        }
        return $this->render('cms/cms_social_post/edit.html.twig', [
            'cms_social_post' => $cmsSocialPost,
            'form' => $form->createView(),
            'index_path' => 'cms_social_post_index',
            'label_title' => 'label.social',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param CmsSocialPost $cmsSocialPost
     * @param Social $social
     * @return Response
     */
    public function delete(Request $request, CmsSocialPost $cmsSocialPost,Social $social): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cmsSocialPost->getId(), $request->request->get('_token'))) {
            $response = $social->deleteFacebookPost($cmsSocialPost);
            if ($response['success'] == true) {
                $cmsSocialPost->setIsPublish(0);
                $cmsSocialPost->setPostStatus('deleted');
                $this->addFlash('success', 'Post deleted successfully.');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
            }
            else {
                $this->addFlash('success', 'There was an issue regarding deletion of post.');
            }
        }

        return $this->redirectToRoute('cms_social_post_index');
    }

}
