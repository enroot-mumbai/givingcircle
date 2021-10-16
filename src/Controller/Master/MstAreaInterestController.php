<?php

namespace App\Controller\Master;

use App\Entity\Master\MstAreaInterest;
use App\Form\Master\MstAreaInterestType;
use App\Repository\Master\MstAreaInterestRepository;
use App\Service\CommonHelper;
use App\Service\FileUploaderHelper;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("core/master/general/area_interest" , name="master_area_interest_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstAreaInterestController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstAreaInterestRepository $mstAreaInterestRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstAreaInterestRepository $mstAreaInterestRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstAreaInterestRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_area_interest/index.html.twig', [
            'mst_area_interests' => $pagination,
            'path_index' => 'master_area_interest_index',
            'path_add' => 'master_area_interest_add',
            'path_edit' => 'master_area_interest_edit',
            'path_show' => 'master_area_interest_show',
            'label_title' => 'label.area_interest',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstAreaInterestRepository $mstAreaInterestRepository
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, MstAreaInterestRepository $mstAreaInterestRepository, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $mstAreaInterest = new MstAreaInterest();
        $sequenceNo = $mstAreaInterestRepository->findOneBySeqNo();
        $mstAreaInterest->setSequenceNo(($sequenceNo[1] + 1));
        $form = $this->createForm(MstAreaInterestType::class, $mstAreaInterest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageContentFile = $form['icon']->getData();
            if (!empty($imageContentFile)) {
                $strSaveFileName = $commonHelper->clean(strtolower($form['areaInterest']->getData())).'icon'
                    .Uuid::uuid4()->toString();
                $newFilename = $fileUploaderHelper->uploadPublicFile($imageContentFile, $strSaveFileName,null);
                $mstAreaInterest->setIcon($newFilename);
            }
            $mstAreaInterest->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstAreaInterest);
            $entityManager->flush();
            return $this->redirectToRoute('master_area_interest_index');
        }

        return $this->render('master/mst_area_interest/form.html.twig', [
            'mst_area_interest' => $mstAreaInterest,
            'form' => $form->createView(),
            'index_path' => 'master_area_interest_index',
            'label_title' => 'label.area_interest',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstAreaInterest $mstAreaInterest
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, MstAreaInterest $mstAreaInterest, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $form = $this->createForm(MstAreaInterestType::class, $mstAreaInterest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageContentFile = $form['icon']->getData();
            if (!empty($imageContentFile)) {
                $strSaveFileName = $commonHelper->clean(strtolower($form['areaInterest']->getData())).'icon'
                    .Uuid::uuid4()->toString();
                $newFilename = $fileUploaderHelper->uploadPublicFile($imageContentFile, $strSaveFileName,null);
                $mstAreaInterest->setIcon($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstAreaInterest);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_area_interest_index');
        }

        return $this->render('master/mst_area_interest/form.html.twig', [
            'mst_area_interest' => $mstAreaInterest,
            'form' => $form->createView(),
            'index_path' => 'master_area_interest_index',
            'label_title' => 'label.area_interest',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstAreaInterest $mstAreaInterest
     * @return Response
     */
    public function delete(Request $request, MstAreaInterest $mstAreaInterest): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstAreaInterest->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstAreaInterest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_area_interest_index');
    }
}
