<?php

namespace App\Controller\Master;

use App\Entity\Master\MstSkillSet;
use App\Form\Master\MstSkillSetType;
use App\Repository\Master\MstSkillSetRepository;
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
 * @Route("core/master/giving_circle/skill_set" , name="master_skill_set_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstSkillSetController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstSkillSetRepository $mstSkillSetRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MstSkillSetRepository $mstSkillSetRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $mstSkillSetRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('master/mst_skill_set/index.html.twig', [
            'mst_skill_sets' => $pagination,
            'path_index' => 'master_skill_set_index',
            'path_add' => 'master_skill_set_add',
            'path_edit' => 'master_skill_set_edit',
            'path_show' => 'master_skill_set_show',
            'label_title' => 'label.skill_set',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param MstSkillSetRepository $mstSkillSetRepository
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, MstSkillSetRepository $mstSkillSetRepository, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $mstSkillSet = new MstSkillSet();
        $form = $this->createForm(MstSkillSetType::class, $mstSkillSet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageContentFile = $form['icon']->getData();
            $imageContentWhiteFile = $form['iconWhite']->getData();
            if (!empty($imageContentFile)) {
                $strSaveFileName = $commonHelper->clean(strtolower($form['skillSet']->getData())).'icon'
                    .Uuid::uuid4()->toString();
                $newFilename = $fileUploaderHelper->uploadPublicFile($imageContentFile, $strSaveFileName,null);
                $mstSkillSet->setIcon($newFilename);
            }
            if (!empty($imageContentWhiteFile)) {
                $strSaveFileName = $commonHelper->clean(strtolower($form['skillSet']->getData())).'whiteicon'
                    .Uuid::uuid4()->toString();
                $newFilename = $fileUploaderHelper->uploadPublicFile($imageContentWhiteFile, $strSaveFileName,null);
                $mstSkillSet->setIconWhite($newFilename);
            }
            $mstSkillSet->setRowId(Uuid::uuid4()->toString());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstSkillSet);
            $entityManager->flush();

            return $this->redirectToRoute('master_skill_set_index');
        }

        return $this->render('master/mst_skill_set/form.html.twig', [
            'mst_skill_set' => $mstSkillSet,
            'form' => $form->createView(),
            'index_path' => 'master_skill_set_index',
            'label_title' => 'label.skill_set',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstSkillSet $mstSkillSet
     * @param FileUploaderHelper $fileUploaderHelper
     * @param CommonHelper $commonHelper
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, MstSkillSet $mstSkillSet, FileUploaderHelper $fileUploaderHelper, CommonHelper $commonHelper): Response
    {
        $form = $this->createForm(MstSkillSetType::class, $mstSkillSet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageContentFile = $form['icon']->getData();
            $imageContentWhiteFile = $form['iconWhite']->getData();
            if (!empty($imageContentFile)) {
                $strSaveFileName = $commonHelper->clean(strtolower($form['skillSet']->getData())).'icon'
                    .Uuid::uuid4()->toString();
                $newFilename = $fileUploaderHelper->uploadPublicFile($imageContentFile, $strSaveFileName,null);
                $mstSkillSet->setIcon($newFilename);
            }
            if (!empty($imageContentWhiteFile)) {
                $strSaveFileName = $commonHelper->clean(strtolower($form['skillSet']->getData())).'iconwhite'
                    .Uuid::uuid4()->toString();
                $newFilename = $fileUploaderHelper->uploadPublicFile($imageContentWhiteFile, $strSaveFileName,null);
                $mstSkillSet->setIconWhite($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('master_skill_set_index');
        }

        return $this->render('master/mst_skill_set/form.html.twig', [
            'mst_skill_set' => $mstSkillSet,
            'form' => $form->createView(),
            'index_path' => 'master_skill_set_index',
            'label_title' => 'label.skill_set',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstSkillSet $mstSkillSet
     * @return Response
     */
    public function delete(Request $request, MstSkillSet $mstSkillSet): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstSkillSet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstSkillSet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_skill_set_index');
    }
}
