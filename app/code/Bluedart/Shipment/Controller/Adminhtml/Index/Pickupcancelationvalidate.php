<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Pickupcancelationvalidate extends \Magento\Backend\App\Action
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
        $this->Pickupcancelation();
    }

    public function Pickupcancelation (){
        $reg_date1 = $this->getRequest()->getParam('reg_date');
        $token_no = $this->getRequest()->getParam('token_no');
        $remarks = $this->getRequest()->getParam('remarks');
        $reg_date = $reg_date1.'T00:00:00+00:00';

        // Store Config Data
        $storeScope     = ScopeInterface::SCOPE_STORE;
        $apitype        = $this->scopeConfig->getValue('bluedart/settings/api_type1', $storeScope);
        $area           = $this->scopeConfig->getValue('bluedart/settings/area', $storeScope);
        $customercode   = $this->scopeConfig->getValue('bluedart/settings/customercode', $storeScope);
        $licencekey     = $this->scopeConfig->getValue('bluedart/settings/licencekey', $storeScope);
        $loginid        = $this->scopeConfig->getValue('bluedart/settings/loginid', $storeScope);
        $password       = $this->scopeConfig->getValue('bluedart/settings/password', $storeScope);
        $version        = $this->scopeConfig->getValue('bluedart/settings/version', $storeScope);
        $pickupregistrationserviceurl  = $this->scopeConfig->getValue('bluedart/url/pickupregistrationservice', $storeScope);

        try{
            if(!$pickupregistrationserviceurl){
                echo "Please enter Pickup Registration Service URL from system configuration.";
                exit;
            }
            /*$soap = new SoapClient($pickupregistrationserviceurl.'?wsdl',
              array(
              'trace'               => 1,  
              'style'               => SOAP_DOCUMENT,
              'use'                 => SOAP_LITERAL,
              'soap_version'        => SOAP_1_2
              ));
            $soap->__setLocation($pickupregistrationserviceurl);
            $soap->sendRequest = true;
            $soap->printRequest = false;
            $soap->formatXML = true;
            //http://netconnect.bluedart.com/Ver1.7/Demo/ShippingAPI/Pickup/PickupRegistrationService.svc
            $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IPickupRegistration/CancelPickup',true);
            $soap->__setSoapHeaders($actionHeader);*/

            $soap = new \Zend\Soap\Client($pickupregistrationserviceurl.'?wsdl');
            $soap->setSoapVersion(SOAP_1_1);

            $params = array(
                'request' => 
                array (
                    //'AWBNo' =>array('58400031395'),
                    'PickupRegistrationDate' => $reg_date1,
                    'Remarks' =>$remarks,
                    'TokenNumber' =>$token_no
                ),
                'profile' => 
                array(
                'Api_type' => $apitype,                          
                'LicenceKey'=>$licencekey,
                'LoginID'=>$loginid,
                'Version'=>$version)
            );
            //$result = $soap->__soapCall('CancelPickup',array($params));
            $result = $soap->CancelPickup($params);
            //echo '<h2>Result</h2><pre>'; print_r($result);exit;
            $StatusInformation = ""; $StatusCode = "";
            
            $StatusCode = $result->CancelPickupResult->Status->CancelPickupResponseStatus->StatusCode;
            $StatusInformation = $result->CancelPickupResult->Status->CancelPickupResponseStatus->StatusInformation;

            if($StatusInformation) { 
                $res_error = $StatusCode." : ".$StatusInformation;
            } else {
                $res_error = $StatusCode;
            }

            if($res_error == 'CancelSuccess'){
                echo "
                <div class='divTable'>
                <div class='headRow'>
                <div class='divCell pincodes' align='center'>
                <p><span><b>Cancel Pickup</b></span></p>
                <p><span><b>IsError :</b></span><span>False</span></p>
                <p><span><b>Error Message :</b></span><span>".$res_error."</span></p>
                </div>
                </div>
                </div>";
            }else{
                echo "
                <div class='divTable'>
                <div class='headRow'>
                <div class='divCell pincodes' align='center'>
                <p><span><b>Cancel Pickup</b></span></p>
                <p><span><b>IsError :</b></span><span class='error_msg'>True</span></p>
                <p><span><b>Error Message :</b></span><span class='error_msg'>".$res_error."</span></p>
                </div>
                </div>
                </div>";
            }
            exit;
        }catch (Exception $e) {
            $this->_messageManager->addError($e->getMessage());
            echo $e->getMessage();exit();
        }
    }   
}
