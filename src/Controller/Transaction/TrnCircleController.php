<?php

namespace App\Controller\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Form\Transaction\TrnCircleEditType;
use App\Form\Transaction\TrnCircleEventsEditType;
use App\Service\MyAccountService;
use App\Service\ProjectService;
use App\Service\NotificationService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Transaction\TrnCircle;
use App\Form\Transaction\TrnCircleUploadType;
use App\Form\Transaction\TrnCircleType;
use App\Repository\Transaction\TrnCircleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;

/**
 * @Route("/core/product/circle", name="product_circle_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class TrnCircleController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param TrnCircleRepository $trnCircleRepository
     * @return Response
     */
    public function index(TrnCircleRepository $trnCircleRepository): Response
    {
        return $this->render('transaction/circle/index.html.twig', [
            'circles' => $trnCircleRepository->findBy(array('isActive' => 1)),
            'path_index' => 'product_circle_index',
            'path_add' => 'product_circle_add',
            'path_edit' => 'product_circle_edit',
            'path_show' => 'product_circle_show',
            'path_upload' => 'product_circle_upload',
            'label_title' => 'label.circle_button',
            'path_comment' => 'product_circle_comment_index',
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
        $trnCircle = new TrnCircle();
        $form = $this->createForm(TrnCircleType::class, $trnCircle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $trnCircle->setCreatedOn(new \DateTime());
            $trnCircle->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $entityManager->persist($trnCircle);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('product_circle_index');
        }

        return $this->render('transaction/circle/form.html.twig', [
            'circle' => $trnCircle,
            'form' => $form->createView(),
            'label_title' => 'label.circle',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param TrnCircle $trnCircle
     * @return Response
     */
    public function show(TrnCircle $trnCircle): Response
    {
        return $this->render('transaction/circle/show.html.twig', [
            'data' => $trnCircle,
            'label_title' => 'label.circle',
            'label_button' => 'label.update',
            'path_index' => 'product_circle_index',
            'path_edit' => 'product_circle_edit',
            'path_delete' => 'product_circle_delete',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param FileUploaderHelper $fileUploaderHelper
     * @param ProjectService $projectService
     * @param NotificationService $notificationService
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function edit(Request $request, TrnCircle $trnCircle, FileUploaderHelper $fileUploaderHelper,
                         ProjectService $projectService, NotificationService $notificationService, MyAccountService
                         $myAccountService): Response
    {
        $form = $this->createForm(TrnCircleEditType::class, $trnCircle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if($form->getData()->getMstStatus()->getStatus() == 'Deactivated' ||
                $form->getData()->getMstStatus()->getStatus() == 'Expired' ||
                $form->getData()->getMstStatus()->getStatus() == 'Rejected') {

                $objStatus = $form->getData()->getMstStatus();
                $projectService->changeProjectEventStatus($trnCircle, $objStatus);
            }

            if($form->getData()->getMstStatus()->getStatus() == 'Activated') {
                $notificationService->setAppUser($trnCircle->getAppUser());
                $notificationService->setTrnCircle($trnCircle);
                $notificationService->doProcess('Project Activation');

                $projectService->sendNotificationToAllUser($trnCircle);
            }
            
            if($form->getData()->getMstStatus()->getStatus() == 'Rejected') {
                $notificationService->setAppUser($trnCircle->getAppUser());
                $notificationService->setTrnCircle($trnCircle);
                $notificationService->doProcess('Project Rejected');
            }

            if($form->getData()->getMstStatus()->getStatus() == 'Deactivated') {
                $notificationService->setAppUser($trnCircle->getAppUser());
                $notificationService->setTrnCircle($trnCircle);
                $notificationService->doProcess('Project Deactivation Creator');

                $arrProjectMemberList =  $myAccountService->getProjectMemberList($trnCircle);
                if(!empty($arrProjectMemberList) && !empty($arrProjectMemberList['arrContributorData'])) {
                    foreach ($arrProjectMemberList['arrContributorData'] as $contributor) {
                        $notificationService->setAppUser($contributor->getAppUser());
                        $notificationService->setTrnCircle($trnCircle);
                        $notificationService->doProcess('Project Deactivation Member');
                    }
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $trnCircle->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $entityManager->persist($trnCircle);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('product_circle_index');
        }

        return $this->render('transaction/circle/edit_form.html.twig', [
            'data' => $trnCircle,
            'form' => $form->createView(),
            'label_title' => 'label.circle',
            'label_button' => 'label.update',
            'mode' => 'edit',
            'path_index' => 'product_circle_index',
        ]);
    }

    /**
     * @Route("/upload/{id}", name="upload", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     */
    public  function upload(Request $request, TrnCircle  $trnCircle, FileUploaderHelper $fileUploaderHelper): Response
    {
        $form = $this->createForm(TrnCircleUploadType::class, $trnCircle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $content = $form->get('trnCircleUploadDocuments');
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($trnCircle->getTrnCircleUploadDocuments() as $key => $trnCircleUploadDocuments) {
                $mediaType = strtolower($content[$key]['mstUploadDocumentType']->getData()->getUploadDocumentType());
                if ($mediaType == 'image' ) {
                    $imageContentFile = $content[$key]['uploadedFilePath']->getData();
                    $newFilename = $fileUploaderHelper->uploadPublicFile($imageContentFile,
                        $content[0]['mediaName']->getData(),null);
                    $trnCircleUploadDocuments->setUploadedFilePath($newFilename);
                }
                $trnCircleUploadDocuments->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                $trnCircleUploadDocuments->setCreatedOn(new \DateTime());
                $trnCircleUploadDocuments->setIsActive(true);
                $trnCircleUploadDocuments->setlocationLatitude($trnCircle->getLocationLatitude());
                $trnCircleUploadDocuments->setLocationLongitude($trnCircle->getLocationLongitude());
            }
            $entityManager->persist($trnCircle);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('product_circle_index');
        }
        return $this->render('transaction/circle/upload.html.twig', [
            'circle' => $trnCircle,
            'form' => $form->createView(),
            'label_title' => 'label.circle',
            'label_button' => 'label.update',
            'path_index' => 'product_circle_index',
            'path_edit' => 'product_circle_edit',
            'path_delete' => 'product_circle_delete',
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @return Response
     */
    public function delete(Request $request, TrnCircle $trnCircle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trnCircle->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trnCircle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_circle_index');
    }

}