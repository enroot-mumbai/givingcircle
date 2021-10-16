<?php


namespace App\Service;

use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnSmsLog;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SendSMS
{
    private $em;
    private $params;
    private $apiKey;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->apiKey = $this->params->get('sms_api_key');
        $this->em = $em;
    }

    /**
     * @param string $strNumber
     * @param string $strMessage
     * @param string $strModuleName
     * @return array
     */
    public function sendSMS($strNumber = "", $strMessage = "", $strModuleName = "") {
        $strNumber = trim($strNumber);
        $arrReturnResponse = array();
        if (empty(trim($strNumber))) {
            $arrReturnResponse = array("status" => 0, "message" => "Mobile Number cannot be blank.");
            return $arrReturnResponse;
        }
        /*if(stripos($strNumber, '+91') !== false) {
            $strNumber = str_ireplace('+', '',$strNumber);
        } elseif (stripos($strNumber, '91') != 0) {
            $arrReturnResponse = array("status" => 0, "message" => "SMS Can be only be sent to Indian Mobile Numbers.");
            return $arrReturnResponse;
        }*/
        // Account details
        $apiKey = urlencode($this->apiKey);

        // Message details
        $numbers = array($strNumber);
        $sender = urlencode('GIVING');
        $message = rawurlencode($strMessage);

        $numbers = implode(',', $numbers);

        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
echo '<pre>'; print_r($data);
        // Send the POST request with cURL
        $ch = curl_init($this->params->get('sms_curl_url'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->saveSMSData($data, $response, $strModuleName);

        if (!empty($response) && $this->isJSON($response) == true) {
            $arrJson = json_decode($response,true);
            if(!empty($arrJson) && isset($arrJson['status']) && $arrJson['status'] == 'success' ) {
                $arrReturnResponse = array("status" => 1, "message" => "SMS Send successfully.");
            } else {
                $arrReturnResponse = array("status" => 0, "message" => "Some error in sending SMS.");
            }
        } else {
            $arrReturnResponse = array("status" => 0, "message" => "Some error in sending SMS.");
        }
        // Process your response here
        echo $response; die;
    }

    /**
     * @param $data
     * @param $response
     */
    private function saveSMSData($data, $response, $strModuleName) {
        $trnSMSLog = new TrnSmsLog();
        $trnSMSLog->setMessage($data['message']);
        $trnSMSLog->setMobileNumber($data['numbers']);
        $trnSMSLog->setSmsRequest(json_encode($data));
        $trnSMSLog->setSmsResponse($response);
        $trnSMSLog->setModuleName($strModuleName);
        $trnSMSLog->setCreatedOn(new DateTime());
        $trnSMSLog->setIsActive(1);
        $trnSMSLog->setOrgCompany($this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
        $this->em->persist($trnSMSLog);
        $this->em->flush();
    }

    /**
     * @param $string
     * @return bool
     */
    function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

    /**
     * @param $parameter
     * @return mixed
     */
    public function getParameter($parameter)
    {
        return $this->params->get($parameter);

    }
}