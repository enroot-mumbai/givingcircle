<?php

namespace App\Controller\Master;

use App\Entity\Master\MstNotificationStatus;
use App\Form\Master\MstNotificationStatusType;
use App\Repository\Master\MstNotificationStatusRepository;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/giving_circle/notification_status" , name="master_notification_status_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstNotificationStatusController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstNotificationStatusRepository $mstNotificationStatusRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstNotificationStatusRepository $mstNotificationStatusRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstNotificationStatusRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_notification_status/index.html.twig', [
            'mst_notification_statuses' => $pagination,
            'path_index' => 'master_notification_status_index',
            'path_add' => 'master_notification_status_add',
            'path_edit' => 'master_notification_status_edit',
            'path_show' => 'master_notification_status_show',
            'label_title' => 'label.notification_status',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstNotificationStatusRepository $mstNotificationStatusRepository
     * @return Response
     */
    public function new(Request $request, MstNotificationStatusRepository $mstNotificationStatusRepository): Response
    {
        $mstNotificationStatus = new MstNotificationStatus();
        $form = $this->createForm(MstNotificationStatusType::class, $mstNotificationStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mstNotificationStatus->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstNotificationStatus);
            $entityManager->flush();

            return $this->redirectToRoute('master_notification_status_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_notification_status' => $mstNotificationStatus,
            'form' => $form->createView(),
            'index_path' => 'master_notification_status_index',
            'label_title' => 'label.notification_status',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstNotificationStatus $mstNotificationStatus
     * @return Response
     */
    public function edit(Request $request, MstNotificationStatus $mstNotificationStatus): Response
    {
        $form = $this->createForm(MstNotificationStatusType::class, $mstNotificationStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_notification_status_index');
        }

        return $this->render('form/form.html.twig', [
            'mst_notification_status' => $mstNotificationStatus,
            'form' => $form->createView(),
            'index_path' => 'master_notification_status_index',
            'label_title' => 'label.notification_status',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstNotificationStatus $mstNotificationStatus
     * @return Response
     */
    public function delete(Request $request, MstNotificationStatus $mstNotificationStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstNotificationStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstNotificationStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_notification_status_index');
    }
}
