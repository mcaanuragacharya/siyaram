<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Waybillcancelationvalidate extends \Magento\Backend\App\Action
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
        $this->Waybillcancelation();
    }

    public function Waybillcancelation (){
        $waybillnumber = $this->getRequest()->getParam('waybillnumber');
        // Store Config Data
        $storeScope     = ScopeInterface::SCOPE_STORE;
        $apitype        = $this->scopeConfig->getValue('bluedart/settings/api_type1', $storeScope);
        $area           = $this->scopeConfig->getValue('bluedart/settings/area', $storeScope);
        $customercode   = $this->scopeConfig->getValue('bluedart/settings/customercode', $storeScope);
        $licencekey     = $this->scopeConfig->getValue('bluedart/settings/licencekey', $storeScope);
        $loginid        = $this->scopeConfig->getValue('bluedart/settings/loginid', $storeScope);
        $password       = $this->scopeConfig->getValue('bluedart/settings/password', $storeScope);
        $version        = $this->scopeConfig->getValue('bluedart/settings/version', $storeScope);
        $waybillgenerationurl  = $this->scopeConfig->getValue('bluedart/url/waybillgeneration', $storeScope);

        try{
            if(!$waybillgenerationurl){
                echo "Please enter Way Bill Generation URL from system configuration.";
                exit;
            }
            /*$soap = new SoapClient($waybillgenerationurl.'?wsdl',
                    array(
                    'trace'               => 1,  
                    'style'               => SOAP_DOCUMENT,
                    'use'                 => SOAP_LITERAL,
                    'soap_version'        => SOAP_1_2
                    ));
            $soap->__setLocation($waybillgenerationurl);
            $soap->sendRequest = true;
            $soap->printRequest = false;
            $soap->formatXML = true;
            $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/CancelWaybill',true);
            $soap->__setSoapHeaders($actionHeader);*/

            $soap = new \Zend\Soap\Client($waybillgenerationurl.'?wsdl');
            $soap->setSoapVersion(SOAP_1_1);

            $params = array('Request'=>
                        array('AWBNo' =>$waybillnumber),
                        'Profile' => 
                        array(
                            'Api_type' => $apitype,                          
                            'LicenceKey'=>$licencekey,
                            'LoginID'=>$loginid,
                            'Version'=>$version
                            )
                        );
            //$result = $soap->__soapCall('CancelWaybill',array($params));
            $result = $soap->CancelWaybill($params);
            //echo '<h2>Result</h2><pre>'; print_r($result);exit;
            if($result->CancelWaybillResult->Status->WayBillGenerationStatus->StatusCode == 'CancelFailure') {
                $res_error = $result->CancelWaybillResult->Status->WayBillGenerationStatus->StatusInformation; 
                echo "
                <div class='divTable'>
                <div class='headRow'>
                <div class='divCell pincodes' align='center'>
                <p><span><b>WayBill Cancellation </b></span></p>
                <p><span><b>IsError :</b></span><span class='error_msg'>True</span></p>
                <p><span><b>Description :</b></span><span class='error_msg'>".$res_error."</span></p>
                </div>
                </div>
                </div>";
            } else {
                $res_status = $result->CancelWaybillResult->Status->WayBillGenerationStatus->StatusInformation;
                echo "
                <div class='divTable'>
                <div class='headRow'>
                <div class='divCell pincodes' align='center'>
                <p><span><b>WayBill Cancellation </b></span></p>
                <p><span><b>Description :</b></span><span><b>".$res_status."</b></span></p>
                </div>
                </div>
                </div>";
            }
            exit;
        }catch (Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
    }   
}
