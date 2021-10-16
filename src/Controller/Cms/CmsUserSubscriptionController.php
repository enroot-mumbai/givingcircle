<?php

namespace App\Controller\Cms;

use App\Entity\Cms\CmsUserSubscription;
use App\Form\Cms\CmsUserSubscriptionType;
use App\Repository\Cms\CmsUserSubscriptionRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/core/cms/usersubscription", name="cms_usersubscription_")
 * @IsGranted("ROLE_SYS_CONTENT_USER")
 */
class CmsUserSubscriptionController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param CmsUserSubscriptionRepository $cmsUserSubscriptionRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(CmsUserSubscriptionRepository $cmsUserSubscriptionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $cmsUserSubscriptionRepository->findAll();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('cms/cms_user_subscription/index.html.twig', [
            'cms_user_subscriptions' => $pagination,
            'path_index' => 'cms_usersubscription_index',
            'path_add' => 'cms_usersubscription_add',
            'path_edit' => 'cms_usersubscription_edit',
            'path_show' => 'cms_usersubscription_show',
            'label_title' => 'label.subscription',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CmsUserSubscription $cmsUserSubscription
     * @return Response
     */
    public function edit(Request $request, CmsUserSubscription $cmsUserSubscription): Response
    {
        $form = $this->createForm(CmsUserSubscriptionType::class, $cmsUserSubscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('isSubscriptionActive')->getData() == false) {
                $cmsUserSubscription->setSubscriptionOptOutDateTime(new DateTime());
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('cms_usersubscription_index');
        }
        return $this->render('cms/cms_user_subscription/form.html.twig', [
            'cms_user_subscription' => $cmsUserSubscription,
            'form' => $form->createView(),
            'index_path' => 'cms_usersubscription_index',
            'label_title' => 'label.subscription',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

}
