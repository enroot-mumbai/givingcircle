<?php

namespace App\Controller\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnProductMedia;
use App\Form\Transaction\TrnProductMediaEditType;
use App\Form\Transaction\TrnProductMediaType;
use App\Repository\Transaction\TrnProductMediaRepository;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;

/**
 * @Route("/core/product/product_media", name="product_media_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class TrnProductMediaController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param TrnProductMediaRepository $trnProductMediaRepository
     * @return Response
     */
    public function index(TrnProductMediaRepository $trnProductMediaRepository): Response
    {
        return $this->render('transaction/media/index.html.twig', [
            'trnProductMedias' => $trnProductMediaRepository->findAll(),
            'path_index' => 'product_media_index',
            'path_add' => 'product_media_add',
            'path_edit' => 'product_media_edit',
            'path_show' => 'product_media_show',
            'path_upload' => 'product_media_upload',
            'label_title' => 'label.media_button',
        ]);
    }
    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $productMedia = new TrnProductMedia();
        $option = array('image_required' => false);
        $form = $this->createForm(TrnProductMediaType::class, $productMedia, $option);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($request->get('trn_product_media')['mediaCollection'] as $key => $mediaContent)
            {
                $trnProductMedia = new TrnProductMedia();
                $trnProductMedia->setOrgCompany($form->get('orgCompany')->getData());
                $trnProductMedia->setMstAreaInterestPrimary($form->get('mstAreaInterestPrimary')->getData());
                $trnProductMedia->setMstAreaInterestSecondary($form->get('mstAreaInterestSecondary')->getData());
                if ($form->get('trnCircle')->getData())
                {
                    $trnProductMedia->setTrnCircle($form->get('trnCircle')->getData());
                }
                if ($form->get('trnCircleEvents')->getData())
                {
                    $trnProductMedia->setTrnCircleEvents($form->get('trnCircleEvents')->getData());
                }
                $mediaType = $mediaContent['mediaType'];
                if ($mediaType == 'image') {

                    $trnProductMedia->setMediaType($mediaContent['mediaType']);
                    $trnProductMedia->setMediaName($mediaContent['mediaName']);
                    $trnProductMedia->setMediaAltText($mediaContent['mediaAltText']);
                    $trnProductMedia->setMediaTitle($mediaContent['mediaTitle']);
                    if (!empty($mediaContent['isActive'])) {
                        $trnProductMedia->setIsActive(1);
                    } else {
                        $trnProductMedia->setIsActive(0);
                    }
                    $imageFile = $request->files->get('trn_product_media')['mediaCollection'][$key]['mediaFileName'];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($imageFile, $commonHelper->slugify($mediaContent['mediaName']), $existingMedia = null);
                    $trnProductMedia->setMediaFileName($newFilename);
                    $trnProductMedia->setUploadedFilePath($this->getParameter('public_file_folder'));
                }
                if ($mediaType == 'video')
                {
                    $trnProductMedia->setMediaName($mediaContent['mediaName']);
                    $trnProductMedia->setMediaURL($mediaContent['mediaURL']);
                }
                $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                $trnProductMedia->setCreatedOn(new DateTime());
                $entityManager->persist($trnProductMedia);

            }



            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('product_media_index');
        }

        return $this->render('transaction/media/form.html.twig', [
            'trnProductMedia' => $productMedia,
            'form' => $form->createView(),
            'index_path' => 'product_media_index',
            'label_title' => 'label.media_button',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }
    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param TrnProductMedia $trnProductMedia
     * @return Response
     */
    public function show(TrnProductMedia $trnProductMedia): Response
    {
        return $this->render('transaction/media/show.html.twig', [
            'data' => $trnProductMedia,
            'label_title' => 'label.circle',
            'label_button' => 'label.update',
            'path_index' => 'product_media_index',
            'path_edit' => 'product_media_edit',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param TrnProductMedia $trnProductMedia
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     */
    public function edit(Request $request, TrnProductMedia $trnProductMedia, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $existingMedia = $trnProductMedia->getMediaFileName();
        $form = $this->createForm(TrnProductMediaEditType::class, $trnProductMedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $mediaType = $form['mediaType']->getData();
            if ($mediaType == 'image' && $form['mediaFileName']->getData() != null) {
                // If there is already a media remove it
                if($existingMedia != '')
                {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($form['mediaFileName']->getData(), $commonHelper->slugify($form['mediaName']->getData()), $existingMedia);
                } else {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($form['mediaFileName']->getData(), $commonHelper->slugify($form['mediaName']->getData()), $existingMedia = null);
                }
                $trnProductMedia->setMediaName($newFilename);
                $trnProductMedia->setMediaURL(null);
                $trnProductMedia->setUploadedFilePath($this->getParameter('public_file_folder'));

            }
            if ($mediaType == 'video') {
                // If there is already a media remove it
                if($existingMedia != '')
                {
                    $fileUploaderHelper->removeFile($existingMedia);
                    $trnProductMedia->setMediaName($form['mediaName']->getData());
                    $trnProductMedia->setMediaAltText(null);
                    $trnProductMedia->setMediaTitle(null);
                    $trnProductMedia->setUploadedFilePath(null);
                }
            }
            $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
            $trnProductMedia->setCreatedOn(new DateTime());
            $entityManager->persist($trnProductMedia);
            $entityManager->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('product_media_index');
        }
        return $this->render('transaction/media/edit.html.twig', [
            'trnProductMedia' => $trnProductMedia,
            'form' => $form->createView(),
            'label_title' => 'label.media_button',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }
}