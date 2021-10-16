<?php

namespace App\Controller\Common;

use App\Entity\Cms\CmsUserSubscription;
use App\Entity\Organization\OrgCompany;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommonController
 * @package App\Controller\Common
 */
class CmsController extends AbstractController
{
    /**
     * @Route("/core/newslettersubscription", name="newsletter-subscription", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function setNewsletterSubscription(Request $request): Response
    {
        $email =  $request->request->get("email");
        if ($email) {

            $checkEmailSubscription = $this->getDoctrine()->getRepository(CmsUserSubscription::class)->findOneBy(['userSubscriptionEmail' => $email]);
            if (empty($checkEmailSubscription)) {

                $cmsUserSubscription = new CmsUserSubscription();
                $entityManager = $this->getDoctrine()->getManager();
                if (!empty($this->getUser())) {
                    $cmsUserSubscription->setAppuser($this->getUser());
                }
                $cmsUserSubscription->setUserSubscriptionEmail(trim($email));
                $cmsUserSubscription->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $cmsUserSubscription->setIsSubscriptionActive(1);
                $cmsUserSubscription->setSubscriptionDateTime(new DateTime('now'));
                $entityManager->persist($cmsUserSubscription);
                $entityManager->flush();
                return $this->json(['message' => 'Thank you for subscribing to our newsletters']);
            } else {
                return $this->json(['message' => 'You have already subscribed for newsletters']);
            }
        } else {
            return $this->json(['message' => 'Email id missing']);
        }

    }

}
