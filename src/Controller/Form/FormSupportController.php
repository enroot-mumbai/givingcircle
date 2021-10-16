<?php

namespace App\Controller\Form;

use App\Entity\Form\FormSupport;
use App\Repository\Form\FormSupportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class FormSupportController extends AbstractController
{
    /**
     * @Route("/core/form/change_maker", name="change_maker", methods={"GET"})
     * @param FormSupportRepository $formSupportRepository
     * @return Response
     */
    public function changeMaker(FormSupportRepository $formSupportRepository): Response
    {
        return $this->render('form/form_change_maker/index.html.twig', [
            'forms' => $formSupportRepository->findBy(['supportForm' => 'changemaker']),
            'path_show' => 'show_change_maker',
            'label_title' => 'label.change_maker',
        ]);
    }

    /**
     * @Route("/core/form/volunteer", name="volunteer", methods={"GET"})
     * @param FormSupportRepository $formSupportRepository
     * @return Response
     */
    public function volunteer(FormSupportRepository $formSupportRepository): Response
    {
        return $this->render('form/form_volunteer/index.html.twig', [
            'forms' => $formSupportRepository->findBy(['supportForm' => 'volunteer']),
            'path_show' => 'show_volunteer',
            'label_title' => 'label.volunteer',
        ]);
    }

    /**
     * @Route("/core/form/change_maker/{id}", name="show_change_maker", methods={"GET"})
     * @param FormSupport $formSupport
     * @return Response
     */
    public function showChangeMaker(FormSupport $formSupport): Response
    {
        return $this->render('form/form_change_maker/show.html.twig', [
            'data' => $formSupport,
            'path_index' => 'change_maker',
            'label_button' => 'label.change_maker',
        ]);
    }

    /**
     * @Route("/core/form/volunteer/{id}", name="show_volunteer", methods={"GET"})
     * @param FormSupport $formSupport
     * @return Response
     */
    public function showVolunteer(FormSupport $formSupport): Response
    {
        return $this->render('form/form_volunteer/show.html.twig', [
            'data' => $formSupport,
            'path_index' => 'volunteer',
            'label_button' => 'label.volunteer',
        ]);
    }


}
