<?php

namespace App\Controller\Master;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Master\MstState;
use App\Form\Master\MstStateType;
use App\Repository\Master\MstStateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/master/place/state", name="master_state_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class MstStateController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param MstStateRepository $mstStatesRepository
     * @param Request $request
     * @return Response
     */
    public function index(MstStateRepository $mstStatesRepository, Request $request): Response
    {
        $mstStates = $mstStatesRepository->findBy(['mstCountry' => $request->query->get('country_id')]);
        return $this->render('master/mst_state/index.html.twig', [
            'mst_states' => $mstStates,
            'path_index' => 'master_state_index',
            'path_add' => 'master_state_add',
            'path_edit' => 'master_state_edit',
            'path_show' => 'master_state_show',
            'label_title' => 'label.state',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $mstState = new MstState();
        $form = $this->createForm(MstStateType::class, $mstState);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mstState);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('master_state_index');
        }

        return $this->render('master/mst_state/form.html.twig', [
            'mst_state' => $mstState,
            'form' => $form->createView(),
            'index_path' => 'master_state_index',
            'label_title' => 'label.state',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param MstState $mstState
     * @return Response
     */
    public function edit(Request $request, MstState $mstState): Response
    {
        $form = $this->createForm(MstStateType::class, $mstState);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('master_state_index');
        }
        return $this->render('master/mst_state/form.html.twig', [
            'mst_state' => $mstState,
            'form' => $form->createView(),
            'index_path' => 'master_state_index',
            'label_title' => 'label.state',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $countryId = trim($request->query->get('countryId'));
        $mstState = $this->getDoctrine()->getRepository(MstState::class)->findBy(['mstCountry' => $countryId]);
        return $this->render('master/mst_state/_ajax_listing.html.twig', [
            'mst_states' => $mstState,
            'path_add' => 'master_state_add',
            'path_edit' => 'master_state_edit',
            'path_show' => 'master_state_show',
            'label_title' => 'label.state',
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param MstState $mstState
     * @return Response
     */
    public function delete(Request $request, MstState $mstState): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mstState->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mstState);
            $entityManager->flush();
        }

        return $this->redirectToRoute('master_state_index');
    }

}
