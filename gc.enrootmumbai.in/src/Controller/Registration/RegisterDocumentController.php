<?php

namespace App\Controller\Registration;

use App\Entity\Master\MstUploadDocumentType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnOrganizationDetails;
use App\Entity\Transaction\TrnOrganizationUploadDocuments;
use App\Form\SystemApp\AppUserRegistrationType;
use App\Form\Transaction\TrnOrganizationUploadDocumentsType;
use App\Form\Transaction\TrnOrganizationUploadType;
use App\Repository\SystemApp\AppUserInfoRepository;
use App\Service\Mailer;
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
 * @Route("/core/registration/document", name="registration_document_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class RegisterDocumentController extends AbstractController
{

    /**
     * @Route("/", name="index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // If register_id param is empty redirect to registration index
        $appUser_id = $request->query->get('appUser_id');
        if(!$appUser_id) {
            return $this->redirectToRoute('registration_register_index');
        }
        $trnOrganizationUploadDocuments = $this->getDoctrine()->getRepository(TrnOrganizationUploadDocuments::class)->findBy(['appUserMemberDetails' => $appUser_id]);

        return $this->render('registration/document/index.html.twig', [
            'documents' => $trnOrganizationUploadDocuments,
            'path_index' => 'registration_document_index',
            'path_add' => 'registration_document_add',
            'path_edit' => 'registration_document_edit',
            'path_show' => 'registration_document_show',
            'label_title' => 'label.document',
        ]);
    }


    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws Exception
     */
    public  function add(Request $request, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        // If register_id param is empty redirect to registration index
        $appUser_id = $request->query->get('appUser_id');
        if(!$appUser_id) {
            return $this->redirectToRoute('registration_register_index');
        }
        $appUser = $this->getDoctrine()->getRepository(AppUser::class)->find($appUser_id);
        $trnOrganizationUploadDocument = new TrnOrganizationUploadDocuments();
        $option = array('file_required' => true);
        $form = $this->createForm(TrnOrganizationUploadDocumentsType::class, $trnOrganizationUploadDocument, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['mediaFileName']->getData();
            if ($file) {
                $strSaveFileName = $form['mediaName']->getData().'-'.$appUser_id.Uuid::uuid4()->toString();
                if ($trnOrganizationUploadDocument->getUploadDocumentPath() != '') {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($file, $commonHelper->slugify($strSaveFileName), $trnOrganizationUploadDocument->getMediaFileName());
                } else {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($file, $commonHelper->slugify($strSaveFileName), $document = null);
                }
                $trnOrganizationUploadDocument->setMediaName($form['mediaName']->getData());
                $trnOrganizationUploadDocument->setMediaFileName($newFilename);
                $trnOrganizationUploadDocument->setUploadDocumentPath($this->getParameter('public_file_folder'));
                $trnOrganizationUploadDocument->setMstUploadDocumentType($this->getDoctrine()->getRepository(MstUploadDocumentType::class)->find(1));
            }
            $trnOrganizationUploadDocument->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $trnOrganizationUploadDocument->setCreatedOn(new DateTime());
            $trnOrganizationUploadDocument->setIsActive(true);
            $trnOrganizationUploadDocument->setAppUserMemberDetails($appUser);
            $trnOrganizationUploadDocument->setOrgCompany($appUser->getAppUserInfo()->getOrgCompany());
            $trnOrganizationUploadDocument->setAppUser($appUser);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($trnOrganizationUploadDocument);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('registration_document_index', $request->query->all());
        }
        return $this->render('registration/document/form.html.twig', [
            'trnOrganizationUploadDocuments' => $trnOrganizationUploadDocument,
            'appUser' => $appUser,
            'form' => $form->createView(),
            'label_title' => 'label.document',
            'label_button' => 'label.create',
            'path_index' => 'registration_document_index',
            'path_edit' => 'registration_document_edit',
            'mode' => 'add'

        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param TrnOrganizationUploadDocuments $trnOrganizationUploadDocument
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws Exception
     */
    public  function edit(Request $request, TrnOrganizationUploadDocuments $trnOrganizationUploadDocument, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        // If register_id param is empty redirect to registration index
        $appUser_id = $request->query->get('appUser_id');
        if(!$appUser_id) {
            return $this->redirectToRoute('registration_register_index');
        }
        $appUser = $this->getDoctrine()->getRepository(AppUser::class)->find($appUser_id);
        $option = array('file_required' => false);
        $form = $this->createForm(TrnOrganizationUploadDocumentsType::class, $trnOrganizationUploadDocument, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['mediaFileName']->getData();
            if ($file) {
                $strSaveFileName = $form['mediaName']->getData().'-'.$appUser_id.Uuid::uuid4()->toString();
                if ($trnOrganizationUploadDocument->getUploadDocumentPath() != '') {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($file, $commonHelper->slugify($strSaveFileName), $trnOrganizationUploadDocument->getMediaFileName());
                } else {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($file, $commonHelper->slugify($strSaveFileName), $document = null);
                }
                $trnOrganizationUploadDocument->setMediaName($form['mediaName']->getData());
                $trnOrganizationUploadDocument->setMediaFileName($newFilename);
                $trnOrganizationUploadDocument->setUploadDocumentPath($this->getParameter('public_file_folder'));
                $trnOrganizationUploadDocument->setMstUploadDocumentType($this->getDoctrine()->getRepository(MstUploadDocumentType::class)->find(1));
            }
            $trnOrganizationUploadDocument->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $trnOrganizationUploadDocument->setCreatedOn(new DateTime());
            $trnOrganizationUploadDocument->setIsActive(true);
            $trnOrganizationUploadDocument->setAppUserMemberDetails($appUser);
            $trnOrganizationUploadDocument->setOrgCompany($appUser->getAppUserInfo()->getOrgCompany());
            $trnOrganizationUploadDocument->setAppUser($appUser);
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($trnOrganizationUploadDocument);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('registration_document_index', $request->query->all());
        }
        return $this->render('registration/document/form.html.twig', [
            'trnOrganizationUploadDocuments' => $trnOrganizationUploadDocument,
            'appUser' => $appUser,
            'form' => $form->createView(),
            'label_title' => 'label.document',
            'label_button' => 'label.update',
            'path_index' => 'registration_document_index',
            'path_edit' => 'registration_document_edit',
            'mode' => 'edit'
        ]);
    }
}