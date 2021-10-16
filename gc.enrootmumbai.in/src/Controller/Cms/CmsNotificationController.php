<?php

namespace App\Controller\Cms;

use App\Entity\Master\MstArticleCategory;
use App\Entity\Organization\OrgCompany;
use App\Service\ArticleUpload;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Cms\CmsNotification;
use App\Form\Cms\CmsNotificationType;
use App\Repository\Cms\CmsNotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;

/**
 * @Route("/core/cms/cms_notifications", name="cms_notifications_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsNotificationController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsNotificationRepository $cmsNotificationRepository
     * @return Response
     */
    public function index(CmsNotificationRepository $cmsNotificationRepository): Response
    {
        return $this->render('cms/cms_notifications/index.html.twig', [
            'cms_notifications' => $cmsNotificationRepository->findAll(),
            'path_index' => 'cms_notifications_index',
            'path_add' => 'cms_notifications_add',
            'path_edit' => 'cms_notifications_edit',
            'path_comment' => 'cms_article_comment_index',
            'path_show' => 'cms_notifications_show',
            'label_title' => 'label.notifications',
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
        $cmsNotification = new CmsNotification();
        $form = $this->createForm(CmsNotificationType::class, $cmsNotification);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $cmsNotification->setRowId(Uuid::uuid4()->toString());
            $cmsNotification->setIsActive(1);
            $entityManager->persist($cmsNotification);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_notifications_index');
        }
        return $this->render('cms/cms_notifications/form.html.twig', [
            'cms_article' => $cmsNotification,
            'form' => $form->createView(),
            'index_path' => 'cms_notifications_index',
            'label_title' => 'label.notifications',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);

    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param CmsNotification $cmsNotification
     * @return Response
     */
    public function show(CmsNotification $cmsNotification): Response
    {
        return $this->render('cms/cms_notifications/show.html.twig', [
            'data' => $cmsNotification,
            'index_path' => 'cms_notifications_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_notifications_index',
            'path_edit' => 'cms_notifications_edit',
            'path_delete' => 'cms_notifications_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsNotification $cmsNotification
     * @param ArticleUpload $articleUpload
     * @param CommonHelper $commonHelper
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     */
    public function edit(Request $request, CmsNotification $cmsNotification, ArticleUpload $articleUpload, CommonHelper $commonHelper, FileUploaderHelper $fileUploaderHelper): Response
    {
        $form = $this->createForm(CmsNotificationType::class, $cmsNotification);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsNotification);
            $entityManager->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_notifications_index');
        }

        return $this->render('cms/cms_notifications/form.html.twig', [
            'cms_article' => $cmsNotification,
            'form' => $form->createView(),
            'index_path' => 'cms_notifications_index',
            'label_title' => 'label.notifications',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }
}
