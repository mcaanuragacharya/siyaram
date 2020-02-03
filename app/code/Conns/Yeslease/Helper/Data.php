<?php
/**
 * Conns
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 * @category    Conns
 * @package     ${Global_Module_Name}
 * @author      Anom Khobragade
 * @copyright   Copyright (c) 2017 Conns (http://www.conns.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Conns\Yeslease\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public function getDelivconfirmStatus($leaseID)
    {
        $errorResponse = false;
        $responseObject = new \stdClass();
        $errorResponseObject = new \stdClass();
        $errorResponseObject->status = 0;
        $errorResponseObject->errorCode = "Y";
        $errorResponseObject->errorDescription = __('Internal Server Error.');
//        $resobj = trim($this->_makeDelivconfirmCall());
        $resobj = '{
            "code": 200,
            "message": "success",
            "data": {
                "LeaseID": 12211212,
                "StoreApplicationIdentifier": "",
                "ApprovalStatus": "Contracts Received",
                "ApprovalLimit": "3000",
                "StatusReason": "",
                "ApprovalReason": "",
                "OkayToDeliver": "true",
                "EsignUrl": "https://esignqa.progressivelp.com/ReleaseCandidate/esign/4595413/9f4fea7e-a3e0-4061-93fc-993bd9ffaaa7/D"
            }
        }
        ';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/makedelivconfirmcall.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Response from Mule -resobj' . $resobj);


        if (!is_string($resobj) || $resobj == "") {
            $errorResponse = true;
        }

        $decodedRespobj = json_decode($resobj);
        if (is_string($resobj) && $resobj != "") {
            if (!is_object($decodedRespobj) && !is_array($decodedRespobj)) {
                $errorResponse = true;
            }

            if (is_array($decodedRespobj)) {
                if (trim($decodedRespobj['code']) != "200" || trim($decodedRespobj['data']) == "" || trim($decodedRespobj['data']) == null) {
                    $errorResponse = true;
                }
            }

            if (is_object($decodedRespobj)) {
                if ($decodedRespobj->code != "200" || $decodedRespobj->data == "" || $decodedRespobj->data == null) {
                    $errorResponse = true;
                }
            }
        }


        if ($errorResponse) {
            if (!empty($decodedRespobj->message)) {
                $errorResponseObject->errorDescription = __($decodedRespobj->message);
            }
            return $errorResponseObject;
        }

        if ($resobj) {
            $resobj = json_decode($resobj);

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/makedelivconfirmcall.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Response from Mule -resobj -- decode' . print_r($resobj, true));

            if ($resobj->code == '200') {
                $responseObject->status = 1;
                $responseObject->errorCode = "";
                $responseObject->errorDescription = "";
                $resp = $resobj->data;
//                $resp = $this->helperData->getDecryptedValue($resp);
//                $this->helperData->connsRestServicelogger('REST response from api starts');
//                $this->helperData->connsRestServicelogger($resp);
//                $this->helperData->connsRestServicelogger('REST response from api ends');

//                $resp = $this->changeRequest($resp);
                $responseObject->Response = $resp;
            } else {
                $responseObject->status = 0;
                $responseObject->errorCode = $resobj->code;
                $responseObject->errorDescription = $resobj->message;
            }

//            $this->helperData->connsRestServicelogger($responseObject);
            return $responseObject;
        }
        return $errorResponseObject;

    }

	/*** 
	Module: Yeslease
	Method: getVerifyLease: get verify Lease API
	**/
	public function getVerifyLease($token, $customerID, $leaseNumber, $last4ssn){

        $enablelog	=	$this->scopeConfig->getValue('payment/progressive/enable_logs');
		$apiUrl		=	"";
		$sslCert	=	"";
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/support_report_progressive.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

		if($this->scopeConfig->getValue('payment/progressive/is_live')==1)
        {
            $apiUrl 	=  $this->scopeConfig->getValue('payment/progressive/live_api_url');
			$sslCert	=  $this->_directoryList->getPath('pub') . "/eai.conns.com.cert";
		}
        else
        {
            $apiUrl 	=  $this->scopeConfig->getValue('payment/progressive/stage_api_url');
			$sslCert	=  $this->_directoryList->getPath('pub') . "/connskeystore.crt";
        }

		$last4ssn	=   $this->getconnsEncrypt256Value($last4ssn);
        $data = array(
            "CustomerID" => "$customerID",
            "LeaseID" => "$leaseNumber",
            "Last4SSN" => "$last4ssn"
        );
	
		$jsonData 	 = 	json_encode($data);
        $apiUrl		.=	'yeslease/verify/lease';
		if($enablelog==1){
            $logger->info("Verify Lease API Url: ".$apiUrl);
			$logger->info(print_r($data, true));
        }

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(400));
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(500));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CAINFO, $sslCert);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Cache-Control: no-cache",
                "Authorization:Bearer $token")
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $result = curl_exec($ch);
        if($enablelog==1){
            $logger->info(print_r($result, true));
        }
        if(!$result){
            if($enablelog==1){
                $this->_logger->info('CURL ERROR:', (array)curl_error($ch));
                $logger->info(print_r(curl_error($ch), true));

            }
        }
        return $result;
    }
	
	
    public function __maketokenapicall()
    {
        $apiUrl = '';
        $data = array();
        $authcode = '';
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/support_progressive.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        if ($this->scopeConfig->getValue('payment/progressive/is_live') == 1) {

            $apiUrl = $this->scopeConfig->getValue('payment/progressive/live_api_url');
            $data = array("grant_type" => "password",
                "username" => $this->scopeConfig->getValue('payment/progressive/live_api_username'),
                "password" => $this->scopeConfig->getValue('payment/progressive/live_api_password'),
                "scope" => "CREDITAPP_API");
            $authcode = base64_encode($this->scopeConfig->getValue('payment/progressive/live_api_auth_header'));

        } else {

            $apiUrl = $this->scopeConfig->getValue('payment/progressive/stage_api_url');
            $data = array("grant_type" => "password",
                "username" => $this->scopeConfig->getValue('payment/progressive/stage_api_username'),
                "password" => $this->scopeConfig->getValue('payment/progressive/stage_api_password'),
                "scope" => "YESLEASE_API");
            $authcode = base64_encode($this->scopeConfig->getValue('payment/progressive/stage_api_auth_header'));

        }

        $data_string = http_build_query($data);
        $apiUrl .= 'oauth/token?' . $data_string;
        if ($this->scopeConfig->getValue('payment/progressive/enable_logs') == 1) {
            $logger->info("progressive Auth API URL:" . $apiUrl);
        }
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(400));
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(500));
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded",
                "Cache-Control: no-cache",
                "Authorization: Basic $authcode"
            )
        );
        $result = curl_exec($ch);
        if ($this->scopeConfig->getValue('payment/progressive/enable_logs') == 1) {
            $logger->info(print_r($result, true));
        }
        if ($result) {

        } else {
            if ($this->scopeConfig->getValue('payment/progressive/enable_logs') == 1) {
                $logger->info(print_r(curl_error($ch), true));
            }
        }
        return $result;
    }

    public function _makeLeaseDetailsCall($token, $leaseID)
    {
        $apiUrl="";
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/support_progressive.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        if($this->scopeConfig->getValue('payment/progressive/is_live')==1)
        {
            $apiUrl =  $this->scopeConfig->getValue('payment/progressive/live_api_url');
        }
        else
        {
            $apiUrl =  $this->scopeConfig->getValue('payment/progressive/stage_api_url');
        }
        $apiUrl = $apiUrl."yeslease/request/details/lease/".$leaseID;
        $logger->info(print_r($apiUrl, true));

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(400));
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(500));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded",
                "Cache-Control: no-cache",
                "Authorization:Bearer $token")
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $result = curl_exec($ch);
        if($this->scopeConfig->getValue('payment/progressive/enable_logs')==1){
            $logger->info(print_r($result, true));
        }
        if(!$result){
            if($this->scopeConfig->getValue('payment/progressive/enable_logs')==1){
                $this->_logger->info('CURL ERROR:', (array)curl_error($ch));
                $logger->info(print_r(curl_error($ch), true));

            }
        }
        return $result;
    }

    public function _makeDelivconfirmCall($token, $leaseID)
    {
        $apiUrl="";
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/support_progressive.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        if($this->scopeConfig->getValue('payment/progressive/is_live')==1)
        {
            $apiUrl =  $this->scopeConfig->getValue('payment/progressive/live_api_url');
        }
        else
        {
            $apiUrl =  $this->scopeConfig->getValue('payment/progressive/stage_api_url');
        }
        $apiUrl = $apiUrl."yeslease/request/deliveryconfirm/lease/".$leaseID;
        $logger->info(print_r($apiUrl, true));

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(400));
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(500));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded",
                "Cache-Control: no-cache",
                "Authorization:Bearer $token")
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $result = curl_exec($ch);
        if($this->scopeConfig->getValue('payment/progressive/enable_logs')==1){
            $logger->info(print_r($result, true));
        }
        if(!$result){
            if($this->scopeConfig->getValue('payment/progressive/enable_logs')==1){
                $this->_logger->info('CURL ERROR:', (array)curl_error($ch));
                $logger->info(print_r(curl_error($ch), true));

            }
        }
        return $result;
    }
	
	public function getCancelLeaseStatus($leaseID){
        $errorResponse = false;
        $responseObject = new \stdClass();
        $errorResponseObject = new \stdClass();
        $errorResponseObject->status = 0;
        $errorResponseObject->errorCode = "Y";
        $errorResponseObject->errorDescription = __('Internal Server Error.');
        $authResult = trim($this->__maketokenapicall());
        if ($authResult){
            $authResult = json_decode($authResult);
            if (isset($authResult->access_token)){
                $resobj = trim($this->_makeCancelLeaseCall($authResult->access_token, $leaseID));
            }
        }
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/support_progressive.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Response from Mule -resobj' . $resobj);
        if (!is_string($resobj) || $resobj == ""){
            $errorResponse = true;
        }
		if (is_string($resobj) && $resobj != ""){
            $decodedRespobj = json_decode($resobj);
            if(!is_object($decodedRespobj) && !is_array($decodedRespobj)){
                $errorResponse = true;
            }
			if (is_array($decodedRespobj)){
                if (trim($decodedRespobj['code']) != "200" || trim($decodedRespobj['data']) == "" || trim($decodedRespobj['data']) == null){
                    $errorResponse = true;
                }
            }
			if (is_object($decodedRespobj)){
                if (trim($decodedRespobj->code) != "200" || trim($decodedRespobj->data) == "" || trim($decodedRespobj->data) == null) {
                    $errorResponse = true;
                }
            }
        }
		if ($errorResponse){
            return $errorResponseObject;
        }
		if ($resobj){
            $resobj = json_decode($resobj);
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/support_progressive.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Response from Mule -resobj -- decode' . print_r($resobj, true));
            if ($resobj->code == '200'){
                $responseObject->status = 1;
                $responseObject->errorCode = "";
                $responseObject->errorDescription = "";
                $resp = $resobj->data;
//                $resp = $this->helperData->getDecryptedValue($resp);
//                $this->helperData->connsRestServicelogger('REST response from api starts');
//                $this->helperData->connsRestServicelogger($resp);
//                $this->helperData->connsRestServicelogger('REST response from api ends');
//                $resp = $this->changeRequest($resp);
                $responseObject->Response = $resp;
            }else{
                $responseObject->status = 0;
                $responseObject->errorCode = $resobj->code;
                $responseObject->errorDescription = $resobj->message;
            }
//            $this->helperData->connsRestServicelogger($responseObject);
            return $responseObject;
        }
        return $errorResponseObject;
    }
	
	public function _makeCancelLeaseCall($token, $leaseID){
        $apiUrl="";
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/support_progressive.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        if($this->scopeConfig->getValue('payment/progressive/is_live')==1){
            $apiUrl =  $this->scopeConfig->getValue('payment/progressive/live_api_url');
        }
        else{
            $apiUrl =  $this->scopeConfig->getValue('payment/progressive/stage_api_url');
        }
        $apiUrl = $apiUrl."yeslease/request/deliveryconfirm/lease/".$leaseID;
        $logger->info(print_r($apiUrl, true));
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(400));
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(500));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded",
                "Cache-Control: no-cache",
                "Authorization:Bearer $token")
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        $result = curl_exec($ch);
        if($this->scopeConfig->getValue('payment/progressive/enable_logs')==1){
            $logger->info(print_r($result, true));
        }
        if(!$result){
            if($this->scopeConfig->getValue('payment/progressive/enable_logs')==1){
                $this->_logger->info('CURL ERROR:', (array)curl_error($ch));
                $logger->info(print_r(curl_error($ch), true));
            }
        }
        return $result;
    }


     /**
     * @param $value
     * @return string
     */
    protected function getconnsEncrypt256Value($value ){
		$key	=	'';
		if($this->scopeConfig->getValue('payment/progressive/is_live')==1){
			$key = $this->scopeConfig->getValue('payment/progressive/live_encrypt_key');
		}else{
			$key = $this->scopeConfig->getValue('payment/progressive/stage_encrypt_key');
		}
        $value = utf8_encode($value);
        $encoded = base64_encode(
            openssl_encrypt($value, "aes-256-cbc", $key, true,str_repeat(chr(0), 16)));
        return rtrim(strtr($encoded, '+/', '-_'), '=');
    }

}