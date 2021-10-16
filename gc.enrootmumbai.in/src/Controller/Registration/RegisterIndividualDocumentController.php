<?php

namespace App\Controller\Registration;

use App\Entity\Master\MstUploadDocumentType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnOrganizationDetails;
use App\Entity\Transaction\TrnOrganizationUploadDocuments;
use App\Entity\Transaction\TrnVolunteerDocument;
use App\Form\SystemApp\AppUserRegistrationType;
use App\Form\Transaction\TrnOrganizationUploadDocumentsType;
use App\Form\Transaction\TrnOrganizationUploadType;
use App\Form\Transaction\TrnVolunteerDocumentsType;
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
 * @Route("/core/registration/individual_document", name="registration_individual_document_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class RegisterIndividualDocumentController extends AbstractController
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
        $appUser = $this->getDoctrine()->getRepository(AppUser::class)->find($appUser_id);

        $trnVolunteerDocuments = array();
        if(!empty($appUser->getTrnVolunterDetail()) && !empty($appUser->getTrnVolunterDetail()->getTrnVolunteerDocuments())) {
            $trnVolunteerDocuments = $appUser->getTrnVolunterDetail()->getTrnVolunteerDocuments();
        }

        return $this->render('registration/individual_document/index.html.twig', [
            'documents' => $trnVolunteerDocuments,
            'path_index' => 'registration_individual_document_index',
            'path_add' => 'registration_individual_document_add',
            'path_edit' => 'registration_individual_document_edit',
            'path_show' => 'registration_individual_document_show',
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
        $trnVolunteerDocument = new TrnVolunteerDocument();
//        $trnOrganizationUploadDocument = new TrnOrganizationUploadDocuments();
        $option = array('file_required' => true);
        $form = $this->createForm(TrnVolunteerDocumentsType::class, $trnVolunteerDocument, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['mediaFileName']->getData();
            if ($file) {
                $strSaveFileName = 'Document-'.$appUser_id.Uuid::uuid4()->toString();
                if ($trnVolunteerDocument->getUploadedFilePath() != '') {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($file, $commonHelper->slugify($strSaveFileName), $trnVolunteerDocument->getUploadedFilePath());
                } else {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($file, $commonHelper->slugify($strSaveFileName), $document = null);
                }
                $trnVolunteerDocument->setDocumentCaption('');
                $trnVolunteerDocument->setUploadedFilePath($newFilename);
//                $trnVolunteerDocument->setMstUploadDocumentType($this->getDoctrine()->getRepository(MstUploadDocumentType::class)->find(1));
            }
            $trnVolunteerDocument->setUploadedOn(new DateTime());
            $trnVolunteerDocument->setIsActive(true);
            $trnVolunteerDocument->setAppUser($appUser);
            $trnVolunteerDocument->setTrnVolunterDetail($appUser->getTrnVolunterDetail());
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($trnVolunteerDocument);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('registration_individual_document_index', $request->query->all());
        }
        return $this->render('registration/individual_document/form.html.twig', [
            'document' => $trnVolunteerDocument,
            'appUser' => $appUser,
            'form' => $form->createView(),
            'label_title' => 'label.document',
            'label_button' => 'label.create',
            'path_index' => 'registration_individual_document_index',
            'path_edit' => 'registration_individual_document_edit',
            'mode' => 'add'

        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param TrnVolunteerDocument $trnVolunteerDocument
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws Exception
     */
    public  function edit(Request $request, TrnVolunteerDocument $trnVolunteerDocument, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $appUser_id = $request->query->get('appUser_id');
        if(!$appUser_id) {
            return $this->redirectToRoute('registration_register_index');
        }
        $appUser = $this->getDoctrine()->getRepository(AppUser::class)->find($appUser_id);
        $option = array('file_required' => false);

        $form = $this->createForm(TrnVolunteerDocumentsType::class, $trnVolunteerDocument, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['mediaFileName']->getData();
            if ($file) {
                $strSaveFileName = 'Document-'.$appUser_id.Uuid::uuid4()->toString();
                if ($trnVolunteerDocument->getUploadedFilePath() != '') {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($file, $commonHelper->slugify($strSaveFileName), $trnVolunteerDocument->getUploadedFilePath());
                } else {
                    $newFilename = $fileUploaderHelper->uploadPublicFile($file, $commonHelper->slugify($strSaveFileName), $document = null);
                }
                $trnVolunteerDocument->setUploadedFilePath($newFilename);
            }
            $trnVolunteerDocument->setUploadedOn(new DateTime());
            $trnVolunteerDocument->setIsActive(true);
            $trnVolunteerDocument->setAppUser($appUser);
            $trnVolunteerDocument->setTrnVolunterDetail($appUser->getTrnVolunterDetail());
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($trnVolunteerDocument);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('registration_individual_document_index', $request->query->all());
        }
        return $this->render('registration/individual_document/form.html.twig', [
            'document' => $trnVolunteerDocument,
            'appUser' => $appUser,
            'form' => $form->createView(),
            'label_title' => 'label.document',
            'label_button' => 'label.update',
            'path_index' => 'registration_individual_document_index',
            'path_edit' => 'registration_individual_document_edit',
            'mode' => 'edit'
        ]);
    }
}