<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Shipmenttransitvalidate extends \Magento\Backend\App\Action
{
    protected $scopeConfig;
    protected $_messageManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->ShipmentTransittime();
    }

    public function ShipmentTransittime (){
        $pPinCodeFrom = $this->getRequest()->getParam('pPinCodeFrom');
        $pPinCodeTo = $this->getRequest()->getParam('pPinCodeTo');
        $pProductCode = strtoupper($this->getRequest()->getParam('pProductCode'));
        $pSubProductCode = strtoupper($this->getRequest()->getParam('pSubProductCode'));
        $pPudate = $this->getRequest()->getParam('pPudate');
        $pPickupTime = $this->getRequest()->getParam('pPickupTime');

        // Store Config Data
        $storeScope     = ScopeInterface::SCOPE_STORE;
        $apitype         = $this->scopeConfig->getValue('bluedart/settings/api_type', $storeScope);
        $area           = $this->scopeConfig->getValue('bluedart/settings/area', $storeScope);
        $customercode   = $this->scopeConfig->getValue('bluedart/settings/customercode', $storeScope);
        $licencekey     = $this->scopeConfig->getValue('bluedart/settings/licencekey', $storeScope);
        $loginid        = $this->scopeConfig->getValue('bluedart/settings/loginid', $storeScope);
        $password       = $this->scopeConfig->getValue('bluedart/settings/password', $storeScope);
        $version        = $this->scopeConfig->getValue('bluedart/settings/version', $storeScope);
        $servicefinderurl  = $this->scopeConfig->getValue('bluedart/url/servicefinder', $storeScope);
        try{
            if(!$servicefinderurl){
                echo "Please enter Service Finder url from system configuration.";
                exit;
            }
            /*$soap = new SoapClient($servicefinderurl.'?wsdl',
                        array(
                        'trace'               => 1,  
                        'style'               => SOAP_DOCUMENT,
                        'use'                 => SOAP_LITERAL,
                        'soap_version'        => SOAP_1_2
                        )
                    );
            $soap->__setLocation($servicefinderurl);
            $soap->sendRequest = true;
            $soap->printRequest = false;
            $soap->formatXML = true;
            $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IServiceFinderQuery/GetDomesticTransitTimeForPinCodeandProduct',true);
            $soap->__setSoapHeaders($actionHeader);*/
            
            $soap = new \Zend\Soap\Client($servicefinderurl.'?wsdl');
            $soap->setSoapVersion(SOAP_1_1);

            $pPudate = date_create($pPudate);
            $pPudate = date_format($pPudate,'Y-m-d');
            $pudate = $pPudate.'T00:00:00+00:00';
            $params = array('pPinCodeFrom' => $pPinCodeFrom,
                        'pPinCodeTo' => $pPinCodeTo,
                        'pProductCode' => $pProductCode,
                        'pSubProductCode' => $pSubProductCode,
                        'pPudate' => $pudate,
                        'pPickupTime' => $pPickupTime,
                        'profile' => 
                        array(
                        'Api_type' => $apitype,
                        'Area'=>$area,
                        'Customercode'=>$customercode,
                        'IsAdmin'=>'',
                        'LicenceKey'=>$licencekey,
                        'LoginID'=>$loginid,
                        'Password'=>$password,
                        'Version'=>$version)
                    );         
            //$result = $soap->__soapCall('GetDomesticTransitTimeForPinCodeandProduct',array($params));
            $result = $soap->GetDomesticTransitTimeForPinCodeandProduct($params);
            //echo "<pre>";print_r($result);exit;

            if($result->GetDomesticTransitTimeForPinCodeandProductResult->ErrorMessage == 'Valid') {
                echo "
                <div class='divTable'>
                <div class='headRow'>
                <div class='divCell pincodes' align='center'>
                <p><span><b>Transit Time Details </b></span></p>
                <p><span><b>Area </b></span><span>".$result->GetDomesticTransitTimeForPinCodeandProductResult->Area."</span></p>
                <p><span><b>City Destination </b></span><span>".$result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Destination."</span></p>
                <p><span><b>City Origin </b></span><span>".$result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Origin."</span></p>
                <p><span><b>EDL Message </b></span><span>".$result->GetDomesticTransitTimeForPinCodeandProductResult->EDLMessage."</span></p>
                <p><span><b>Expected Delivery Date </b></span><span>".$result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDateDelivery."</span></p>
                <p><span><b>Expected POD Date </b></span><span>".$result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDatePOD."</span></p>
                <p><span><b>Service Center </b></span><span>".$result->GetDomesticTransitTimeForPinCodeandProductResult->ServiceCenter."</span></p>
                <p><span><b>Additional Days </b></span><span>".$result->GetDomesticTransitTimeForPinCodeandProductResult->AdditionalDays."</span></p>
                </div>
                </div>
                </div>";
            } else {
                if($result->GetDomesticTransitTimeForPinCodeandProductResult->ErrorMessage != "") {
                    $res_error = $result->GetDomesticTransitTimeForPinCodeandProductResult->ErrorMessage;
                } else {
                    $res_error = "Invalid Input"; 
                } 
                echo "
                <div class='divTable'>
                <div class='headRow'>
                <div class='divCell pincodes' align='center'>
                <p><span><b>Transit Time Details </b></span></p>
                <p><span><b>IsError </b></span><span class='error_msg'>True</span></p>
                <p><span><b>Description </b></span><span class='error_msg'>".$res_error."</span></p>
                </div>
                </div>
                </div>";
            }
        }catch (Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
    }
}
