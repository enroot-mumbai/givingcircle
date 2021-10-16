<?php

namespace App\Service\PaymentGateway\PayU;

use App\Entity\Master\MstPaymentGatewayDetail;
use App\Entity\Transaction\TrnOrder;
use App\Entity\Transaction\TrnOrderDetail;
use App\Repository\SystemApp\AppUserRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class PayU
 * @package App\Service\PaymentGateway\PayU
 */
class PayU
{
    private $em;
    private $security;
    private $session;
    private $userProvider;
    private $appUserRepository;
    public function __construct(EntityManagerInterface $em, Security $security, SessionInterface $session,
                                UserProviderInterface $userProvider, AppUserRepository $appUserRepository)
    {
        $this->em = $em;
        $this->security = $security;
        $this->session = $session;
        $this->userProvider = $userProvider;
        $this->appUserRepository = $appUserRepository;
    }

    /**
     * @param $env
     * @return array
     */
    public function createTransaction($env): array
    {
        $paymentSystems = $this->em->getRepository(MstPaymentGatewayDetail::class)->findBy(['paymentGatewayEnv' => $env, 'mstPaymentGateway' => 1, 'isActive' => 1]);
        foreach ($paymentSystems as $paymentSystem)
        {
            $paySystem[$paymentSystem->getPaymentKey()] = $paymentSystem->getPaymentKeyValue();
        }
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->session->set('payuSalt', $paySystem['salt']);
        $transaction['url'] = $paySystem['url'];
        $transaction['key'] = $paySystem['key'];
        $transaction['surl'] = 'https://'.$_SERVER['HTTP_HOST'].'/payment-success';
        $transaction['furl'] = 'https://'.$_SERVER['HTTP_HOST'].'/payment-failure';
        $transaction['curl'] = 'https://'.$_SERVER['HTTP_HOST'].'/payment-cancel';
        $trnOrder = $this->em->getRepository(TrnOrder::class)->findOneBy(['cartId' => $this->session->get('cartId')]);
        $transaction['firstname'] = $trnOrder->getUserFirstName();
        $transaction['lastname'] = $trnOrder->getUserLastName();
        $transaction['email'] = $trnOrder->getUserLastName();
        $transaction['phone'] = $trnOrder->getUserMobileNo();
        if (!empty($this->security->getUser()))
            $transaction['address1'] = $this->security->getUser()->getId();
        else
            $transaction['address1'] = null;
        $transaction['address2'] = $this->session->getId();
        $transaction['city'] = $this->session->get('cartId');
        $trnOrderDetail = $this->em->getRepository(TrnOrderDetail::class)->findOneBy(['trnOrder' => $trnOrder->getId()]);
        $transaction['txnid'] = $trnOrder->getCartTokenId();

        if(!empty($trnOrderDetail->getChangeMakerArticle())) {
            $transaction['productinfo'] = 'Donation for GC via'. $trnOrderDetail->getChangeMakerArticle()->getArticleFor();
        } else {
            if(!empty($trnOrderDetail->getTrnFundRaiserCircleEventSubEvents())) {
                $transaction['productinfo'] = $trnOrderDetail->getTrnFundRaiserCircleEventSubEvents()->getSubEventName();
            } else {
                $transaction['productinfo'] = 'CrowdFunding for '. $trnOrderDetail->getTrnCircleEvent()->getName();
            }
        }

        $transaction['amount'] = $trnOrder->getTransactionAmount();
        $transaction['hash'] = hash('sha512', $paySystem['key'].'|'.$transaction['txnid'].'|'.$transaction['amount'].'|'.$transaction['productinfo'].'|'.$transaction['firstname'].'|'.$transaction['email'].'|||||||||||'.$paySystem['salt']);
        return $transaction;
    }

    /**
     * @param $postData
     * @return string
     * @throws Exception
     */
    public function verifyTransaction($postData)
    {
        $data = json_encode($postData);
        $trnOrderId = $this->em->getRepository(TrnOrder::class)->findOneBy(['cartTokenId' => $postData['txnid'], 'cartId' => $postData['city']]);
        $this->session->setId($postData['address2']);
        $this->session->set('cartId', $postData['city']);
        if(!empty($postData['address1'])) {
            $appUser = $this->appUserRepository->find($postData['address1']);
            $this->userProvider->loadUserByUsername($appUser->getUsername());
        }
        //$this->session->set('UserId', $postData['address1']);
        $trnOrderDetail = $this->em->getRepository(TrnOrderDetail::class)->findOneBy(['trnOrder' => $trnOrderId->getId()]);
        // Order No Generation
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $monthFormat = $now->format('m');
        $yearFormat = $now->format('y');

        $trnCircleEvent = $trnOrderId->getTrnCircleEvent();
        $trnFundRaiserCircleEventDetails = null;
        $trnCrowdFundEvent = null;

        if(!empty($trnOrderId->getChangeMakerArticle())) {
            $changeMakerArticle = $trnOrderId->getChangeMakerArticle();
        } else {
            if(!empty($trnCircleEvent->getTrnFundRaiserCircleEventDetails())) {
                $arrTrnFundRaiserCircleEventDetails = $trnCircleEvent->getTrnFundRaiserCircleEventDetails();
                if (!empty($arrTrnFundRaiserCircleEventDetails) && !empty($arrTrnFundRaiserCircleEventDetails[0])) {
                    $trnFundRaiserCircleEventDetails = $arrTrnFundRaiserCircleEventDetails[0];
                }
            }

            $trnCrowdFundEvent = $trnOrderId->getTrnCrowdFundEvent();

        }

        $userOrderNo = $userInvoiceNo = "";

        if(!empty($changeMakerArticle)) {
            $userOrderNo = 'AHG/O/' . $monthFormat . '' . $yearFormat . '/chngmk/' . $trnOrderId->getId(). '/'.$changeMakerArticle->getId();
            $userInvoiceNo = 'AHG/I/' . $monthFormat . '' . $yearFormat . '/chngmk/' . $trnOrderId->getId(). '/'.$changeMakerArticle->getId();
        } else {
            if (!empty($trnFundRaiserCircleEventDetails)) {
                $userOrderNo = 'AHG/O/' . $monthFormat . '' . $yearFormat . '/' . $trnCircleEvent->getId() . '/' . $trnOrderId->getId() . '/' . $trnFundRaiserCircleEventDetails->getId();
                $userInvoiceNo = 'AHG/I/' . $monthFormat . '' . $yearFormat . '/' . $trnCircleEvent->getId() . '/' . $trnOrderId->getId() . '/' . $trnFundRaiserCircleEventDetails->getId();;
            } elseif (!empty($trnCrowdFundEvent)) {
                $userOrderNo = 'AHG/O/' . $monthFormat . '' . $yearFormat . '/' . $trnCircleEvent->getId() . '/' . $trnOrderId->getId() . '/' . $trnCrowdFundEvent->getId();
                $userInvoiceNo = 'AHG/I/' . $monthFormat . '' . $yearFormat . '/' . $trnCircleEvent->getId() . '/' . $trnOrderId->getId() . '/' . $trnCrowdFundEvent->getId();
            }
        }

        $this->em->getRepository(TrnOrder::class)->updateTransaction($data, $postData['mihpayid'], $postData['status'], $trnOrderId->getId(), $userOrderNo, $userInvoiceNo);
        if (isset($postData ['key'])) {
            $key				=   $postData['key'];
            $txnid 				= 	$postData['txnid'];
            $amount      		= 	$postData['amount'];
            $productInfo  		= 	$postData['productinfo'];
            $firstname    		= 	$postData['firstname'];
            $email        		=	$postData['email'];
            $status				= 	$postData['status'];
            $resphash			= 	$postData['hash'];
            $this->session->setId($postData['address2']);
            $this->session->set('cartId', $postData['city']);
            if(!empty($postData['address1'])) {
                $appUser = $this->appUserRepository->find($postData['address1']);
                $this->userProvider->loadUserByUsername($appUser->getUsername());
            }

            $salt               =   $this->session->get('payuSalt');
            //Calculate response hash to verify
            $keyString 	  		=  	$key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'||||||||||';
            $keyArray 	  		= 	explode("|",$keyString);
            $reverseKeyArray 	= 	array_reverse($keyArray);
            $reverseKeyString	=	implode("|",$reverseKeyArray);
            $CalcHashString 	= 	strtolower(hash('sha512', $salt.'|'.$status.'|'.$reverseKeyString)); //hash without additionalcharges
            //check for presence of additionalcharges parameter in response.
            $additionalCharges  = 	"";

            If (isset($postData["additionalCharges"])) {
                $additionalCharges=$postData["additionalCharges"];
                //hash with additionalcharges
                $CalcHashString 	= 	strtolower(hash('sha512', $additionalCharges.'|'.$salt.'|'.$status.'|'.$reverseKeyString));
            }
            //Compare status and hash. Hash verification is mandatory.
            if ($status == 'success'  && $resphash == $CalcHashString) {
                $msg = "Transaction Successful, Hash Verified...<br />";
                //Additional step - Use verify payment api to double check payment.
                if($this->verifyPayment($key,$salt,$txnid,$status)) {
                    $msg = "success";
                } else {
                    $msg = "Your Transaction is successfull, but your payment is not verified with the Payment gateway";
                }
            }
            else if ($status == 'failure'  && $resphash == $CalcHashString) {
                $errorMessage = $postData['error_Message'];
                $msg = "Your Transaction has failed. Reason: $errorMessage";
            }
            else {
                $msg = "Your Transaction failed due to data mismatch";
            }
        }
        $this->session->remove('payuSalt');
        return $msg;

    }

    /**
     * @param $key
     * @param $salt
     * @param $txnid
     * @param $status
     * @return bool
     */
    public function verifyPayment($key,$salt,$txnid,$status)
    {
        $command = "verify_payment"; //mandatory parameter
        $hash_str = $key  . '|' . $command . '|' . $txnid . '|' . $salt ;
        $hash = strtolower(hash('sha512', $hash_str)); //generate hash for verify payment request
        $r = array('key' => $key , 'hash' =>$hash , 'var1' => $txnid, 'command' => $command);
        $qs= http_build_query($r);

        if ($_SERVER['SERVER_NAME'] == 'www.givingcircle.in') {
            //for live
            $wsUrl = "https://info.payu.in/merchant/postservice.php?form=2";
        } else {
            //for test
            $wsUrl = "https://test.payu.in/merchant/postservice.php?form=2";
        }
        try
        {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $wsUrl);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
            curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_SSLVERSION, 6);
            curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
            $o = curl_exec($c);
            if (curl_errno($c)) {
                $sad = curl_error($c);
                throw new Exception($sad);
            }
            curl_close($c);
            $response = json_decode($o,true);
            $data = $o; // Store the data in log
            $trnOrderId = $this->em->getRepository(TrnOrder::class)->findOneBy(['cartTokenId' => $txnid, 'cartId' => $this->session->get('cartId')]);
            $this->em->getRepository(TrnOrder::class)->updateVerifyPaymentLog($data, $response['transaction_details'][$txnid]['status'], $trnOrderId->getId());

            if(isset($response['status']))
            {
                // response is in Json format. Use the transaction_detailspart for status
                $response = $response['transaction_details'];
                $response = $response[$txnid];
                if($response['status'] == $status) //payment response status and verify status matched
                    return true;
                else
                    return false;
            }
            else {
                return false;
            }
        }
        catch (Exception $e){
            return false;
        }
    }

}
