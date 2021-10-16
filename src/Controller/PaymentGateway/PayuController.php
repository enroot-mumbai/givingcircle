<?php

namespace App\Controller\PaymentGateway;

use App\Entity\Transaction\TrnOrder;
use App\Entity\Transaction\TrnOrderDetail;
use App\Service\Mailer;
use App\Service\PaymentGateway\PayU\PayU;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PayuController
 * @package App\Controller\PaymentGateway
 */

class PayuController extends AbstractController
{
    /**
     * @Route("/payu", name="payu", methods={"GET","POST"})
     * @param Request $request
     * @param payU $payU
     * @return Response
     */
    public function payUCreateTransaction(Request $request, PayU $payU): Response
    {
        if ($_SERVER['SERVER_NAME'] == 'www.givingcircle.in') { //TODO
            $env = 'Production';
        }
        else {
            $env = 'test';
        }

        // call the form page and redirect to payu payment gateway
        return $this->render('portal/payment_gateway/payu/index.html.twig', [
            'transaction' => $payU->createTransaction($env)

        ]);
    }

    /**
     * @Route("/payment-success", name="payment-success", methods={"GET","POST"})
     * @param Request $request
     * @param payU $payU
     * @param SessionInterface $session
     * @param Mailer $mailer
     * @return Response
     */

    public function paymentSuccess(Request $request, PayU $payU, SessionInterface $session, Mailer $mailer): Response
    {
        $verifyResponse = $payU->verifyTransaction($_POST);
        if ($verifyResponse == 'success') {
            // Redirect the user to specific thank you page based on product
            $orderStatus = $this->getDoctrine()->getRepository(TrnOrder::class)->findOneBy(['cartId' => $session->get('cartId')]);
            $trnOrderDetail = $this->getDoctrine()->getRepository(TrnOrderDetail::class)->findOneBy(['trnOrder' => $orderStatus->getId()]);
            $session->set('trnOrderId', $orderStatus->getId());
            $userEmail = $orderStatus->getUserEmail();
            if (!empty($userEmail)) {
                $userName = $orderStatus->getUserFirstName(). ' '. $orderStatus->getUserLastName();
                $amount = $orderStatus->getTransactionAmount();

                if(!empty($orderStatus->getChangeMakerArticle())) {
                    $eventName = '';
                    $projectName = 'Giving Circle';
                } else {
                    $eventName = $orderStatus->getTrnCircleEvent()->getName();
                    $projectName = $orderStatus->getTrnCircleEvent()->getTrnCircle()->getCircle();
                }
                $mailer->sendSuccessfulPayment($userEmail, $userName, $amount, $eventName, $projectName);

                //Check if Event is 80G Type
                $certificateNo = "";
                if (!empty($certificateNo)) {

                    $mailer->sendSuccessfulPayment80G($userEmail, $userName, $amount, $eventName, $projectName, $certificateNo);
                }
                //Check if Event is 80G Type
            }
            return $this->redirectToRoute('payment-thank-you');
        } else {
            return $this->render('portal/payment_gateway/payu/message.html.twig', [
                'message' => $verifyResponse,
            ]);
        }
    }

    /**
     * @Route("/payment-failure", name="payment-failure", methods={"GET","POST"})
     * @param Request $request
     * @param payU $payU
     * @return Response
     */

    public function paymentFailure(Request $request, PayU $payU): Response
    {

        return $this->render('portal/payment_gateway/payu/message.html.twig', [
            'message' => $payU->verifyTransaction($_POST)
        ]);

    }

    /**
     * @Route("/payment-cancel", name="payment-cancel", methods={"POST"})
     * @param Request $request
     * @param PayU $payU
     * @return Response
     */

    public function paymentCancel(Request $request, PayU $payU): Response
    {

        return $this->render('portal/payment_gateway/payu/message.html.twig', [
            'message' => $payU->verifyTransaction($_POST)
        ]);

    }

}
