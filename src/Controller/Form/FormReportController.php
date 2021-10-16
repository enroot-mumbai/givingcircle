<?php

namespace App\Controller\Form;

use App\Entity\Form\FormReport;
use App\Repository\Form\FormReportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class FormReportController extends AbstractController
{
    /**
     * @Route("/core/form/report_self", name="report_self", methods={"GET"})
     * @param FormReportRepository $formReportRepository
     * @return Response
     */
    public function reportSelf(FormReportRepository $formReportRepository): Response
    {
        $resultSet = $formReportRepository->findBy(['reportFor' => 'self'],['createDateTime' => 'DESC']);

        return $this->render('form/form_report_self/index.html.twig', [
            'forms' => $resultSet,
            'path_show' => 'show_report_self',
            'label_title' => 'label.self',
        ]);
    }

    /**
     * @Route("/core/form/report_other", name="report_other", methods={"GET"})
     * @param FormReportRepository $formReportRepository
     * @return Response
     */
    public function reportOther(FormReportRepository $formReportRepository): Response
    {
        return $this->render('form/form_report_other/index.html.twig', [
            'forms' => $formReportRepository->findBy(['reportFor' => 'other']),
            'path_show' => 'show_report_other',
            'label_title' => 'label.other',
        ]);
    }

    /**
     * @Route("/core/form/report_self/{id}", name="show_report_self", methods={"GET"})
     * @param FormReport $formReport
     * @return Response
     */
    public function showReportSelf(FormReport $formReport): Response
    {
        return $this->render('form/form_report_self/show.html.twig', [
            'data' => $formReport,
            'path_index' => 'report_self',
            'label_button' => 'label.self',
        ]);
    }

    /**
     * @Route("/core/form/report_other/{id}", name="show_report_other", methods={"GET"})
     * @param FormReport $formReport
     * @return Response
     */
    public function showReportOther(FormReport $formReport): Response
    {
        return $this->render('form/form_report_other/show.html.twig', [
            'data' => $formReport,
            'path_index' => 'report_other',
            'label_button' => 'label.other',
        ]);
    }


}
