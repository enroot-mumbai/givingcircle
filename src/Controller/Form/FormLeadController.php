<?php

namespace App\Controller\Form;

use App\Entity\Form\FormLead;
use App\Form\Form\FormLeadType;
use App\Repository\Form\FormLeadRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core/form/lead", name="form_lead_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class FormLeadController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param FormLeadRepository $formLeadRepository
     * @return Response
     */
    public function index(FormLeadRepository $formLeadRepository): Response
    {
        return $this->render('form/form_lead/index.html.twig', [
            'form_leads' => $formLeadRepository->findAll(),
            'path_index' => 'form_lead_index',
            'path_add' => 'form_lead_add',
            'path_edit' => 'form_lead_edit',
            'path_show' => 'form_lead_show',
            'label_title' => 'label.lead',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $formLead = new FormLead();
        $form = $this->createForm(FormLeadType::class, $formLead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formLead->setLeadCreateTime(new DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formLead);
            $entityManager->flush();
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('form_lead_index');
        }

        return $this->render('form/form_lead/form.html.twig', [
            'form_lead' => $formLead,
            'form' => $form->createView(),
            'label_title' => 'label.lead',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param FormLead $formLead
     * @return Response
     */
    public function show(FormLead $formLead): Response
    {
        return $this->render('form/form_lead/show.html.twig', [
            'data' => $formLead,
            'path_index' => 'form_lead_index',
            'path_edit' => 'form_lead_edit',
            'path_convert' => 'form_lead_convert',
            'path_delete' => 'form_lead_delete',
            'label_button' => 'label.lead',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param FormLead $formLead
     * @return Response
     */
    public function edit(Request $request, FormLead $formLead): Response
    {
        $form = $this->createForm(FormLeadType::class, $formLead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('form_lead_index');
        }

        return $this->render('form/form_lead/form.html.twig', [
            'form_lead' => $formLead,
            'form' => $form->createView(),
            'label_title' => 'label.lead',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param FormLead $formLead
     * @return Response
     */
    public function delete(Request $request, FormLead $formLead): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formLead->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formLead);
            $entityManager->flush();
        }

        return $this->redirectToRoute('form_lead_index');
    }
}
