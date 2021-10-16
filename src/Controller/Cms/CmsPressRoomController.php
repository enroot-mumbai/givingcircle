<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsPressRoom;
use App\Form\Cms\CmsPressRoomType;
use App\Repository\Cms\CmsPressRoomRepository;
use App\Service\FileUploaderHelper;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/core/cms/pressroom", name="cms_pressroom_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsPressRoomController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsPressRoomRepository $cmsPressRoomRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(CmsPressRoomRepository $cmsPressRoomRepository, Request $request): Response
    {
        $pressReleases = $cmsPressRoomRepository->findBy(array(), array('articleDateTime' => 'DESC'));
        return $this->render('cms/cms_press_room/index.html.twig', [
            'cms_press_rooms' => $pressReleases,
            'path_index' => 'cms_pressroom_index',
            'path_add' => 'cms_pressroom_add',
            'path_edit' => 'cms_pressroom_edit',
            'path_show' => 'cms_pressroom_show',
            'label_title' => 'label.pressroom',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileUploaderHelper $fileUploaderHelper): Response
    {
        $cmsPressRoom = new CmsPressRoom();
        $form = $this->createForm(CmsPressRoomType::class, $cmsPressRoom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cmsPressRoom->setRowId(Uuid::uuid4()->toString());

            $articleIntroFile = $form['articleIntroDesktopImage']->getData();
            if ($articleIntroFile) {
                $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroFile, $form['articleIntroImageSetName']->getData());
                $cmsPressRoom->setArticleIntroDesktopImage($newFilename);
                $cmsPressRoom->setArticleIntroDesktopImagePath($this->getParameter('public_file_folder'));
                $cmsPressRoom->setArticleIntroImageSetName($form['articleIntroImageSetName']->getData());
                $cmsPressRoom->setArticleIntroImageAlt($form['articleIntroImageAlt']->getData());
                $cmsPressRoom->setArticleIntroImageTitle($form['articleIntroImageTitle']->getData());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsPressRoom);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('cms_pressroom_index');
        }

        /*
        return $this->render('form/form.html.twig', [
            'cms_press_room' => $cmsPressRoom,
            'form' => $form->createView(),
            'index_path' => 'cms_pressroom_index',
            'label_title' => 'label.pressroom',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
        */
        return $this->render('cms/cms_press_room/form.html.twig', [
            'cms_press_room' => $cmsPressRoom,
            'form' => $form->createView(),
            'index_path' => 'cms_pressroom_index',
            'label_title' => 'label.pressroom',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param CmsPressRoom $cmsPressRoom
     * @return Response
     */
    public function show(CmsPressRoom $cmsPressRoom): Response
    {
        return $this->render('cms/cms_press_room/show.html.twig', [
            'data' => $cmsPressRoom,
            'index_path' => 'cms_pressroom_delete',
            'label_button' => 'label.delete',
            'path_index' => 'cms_pressroom_index',
            'path_edit' => 'cms_pressroom_edit',
            'path_delete' => 'cms_pressroom_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CmsPressRoom $cmsPressRoom
     * @return Response
     */
    public function edit(Request $request, CmsPressRoom $cmsPressRoom, FileUploaderHelper $fileUploaderHelper): Response
    {
        $form = $this->createForm(CmsPressRoomType::class, $cmsPressRoom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $articleIntroFile = $form['articleIntroDesktopImage']->getData();

            if ($articleIntroFile) {
                $newFilename = $fileUploaderHelper->uploadPublicFile($articleIntroFile, $form['articleIntroImageSetName']->getData());
                $cmsPressRoom->setArticleIntroDesktopImage($newFilename);
                $cmsPressRoom->setArticleIntroDesktopImagePath($this->getParameter('public_file_folder'));
                $cmsPressRoom->setArticleIntroImageSetName($form['articleIntroImageSetName']->getData());
                $cmsPressRoom->setArticleIntroImageAlt($form['articleIntroImageAlt']->getData());
                $cmsPressRoom->setArticleIntroImageTitle($form['articleIntroImageTitle']->getData());
            }


            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_pressroom_index');
        }
        /*
        return $this->render('form/form.html.twig', [
            'cms_press_room' => $cmsPressRoom,
            'form' => $form->createView(),
            'index_path' => 'cms_pressroom_index',
            'label_title' => 'label.pressroom',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
        */
        return $this->render('cms/cms_press_room/form.html.twig', [
            'cms_press_room' => $cmsPressRoom,
            'form' => $form->createView(),
            'index_path' => 'cms_pressroom_index',
            'label_title' => 'label.pressroom',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param CmsPressRoom $cmsPressRoom
     * @return Response
     */
    public function delete(Request $request, CmsPressRoom $cmsPressRoom): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cmsPressRoom->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsPressRoom);
            $entityManager->flush();
        }
        return $this->redirectToRoute('cms_pressroom_index');
    }
}
