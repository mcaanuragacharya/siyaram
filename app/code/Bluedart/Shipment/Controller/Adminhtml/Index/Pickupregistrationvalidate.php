<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Pickupregistrationvalidate extends \Magento\Backend\App\Action
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
        $this->Pickupregistration();
    }

    public function Pickupregistration (){
        $area = $this->getRequest()->getParam('area');
        $customercode = strtoupper($this->getRequest()->getParam('customercode'));
        $clientname = $this->getRequest()->getParam('clientname');
        $AWBNo = $this->getRequest()->getParam('awb_no');
        $PackType = $this->getRequest()->getParam('packtype');
        $isToPayShipper = $this->getRequest()->getParam('isToPayShipper');
        $IsReversePickup = $this->getRequest()->getParam('IsReversePickup');
        $IsForcePickup = $this->getRequest()->getParam('IsForcePickup');
        $IsDDN = $this->getRequest()->getParam('IsDDN');
        $chkval_shipper = "";  
        if($isToPayShipper == 0){
          $chkval_shipper = "N"; 
        } else if($isToPayShipper == 1){
          $chkval_shipper = "Y";    
        }
        $DoxNDox  = $this->getRequest()->getParam('DoxNDox');
        $address1 = $this->getRequest()->getParam('address1');
        $address2 = $this->getRequest()->getParam('address2');
        $address3 = $this->getRequest()->getParam('address3');
        $pincode = $this->getRequest()->getParam('pincode');
        $mobile = $this->getRequest()->getParam('mobile');
        $telephone = $this->getRequest()->getParam('telephone');
        $contactperson = $this->getRequest()->getParam('contactperson');
        $email = $this->getRequest()->getParam('email');
        $pickupdate = $this->getRequest()->getParam('pickupdate');
        $pickupdate = date_create($pickupdate);
        $pickupdate = date_format($pickupdate,'Y-m-d');
        $pickupreadytime = $this->getRequest()->getParam('pickupreadytime');
        $ofcclosingtime = $this->getRequest()->getParam('ofcclosingtime');
        $pieces = $this->getRequest()->getParam('pieces');
        $productcode = $this->getRequest()->getParam('productcode');
        $actweight = $this->getRequest()->getParam('actweight');
        $volwt = $this->getRequest()->getParam('volwt');
        $chktopay = $this->getRequest()->getParam('chktopay');
        $routecode = $this->getRequest()->getParam('routecode');
        $remarks = $this->getRequest()->getParam('remarks');
        $refno = $this->getRequest()->getParam('refno');

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
                echo "Please enter Pickup Registration Service url from system configuration.";
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
            $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IPickupRegistration/RegisterPickup',true);
            $soap->__setSoapHeaders($actionHeader);*/

            $soap = new \Zend\Soap\Client($pickupregistrationserviceurl.'?wsdl');
            $soap->setSoapVersion(SOAP_1_1);

            $params = array(
                'request' => 
                array (
                'AWBNo' => array($AWBNo),
                'AreaCode' => $area,
                'ContactPersonName' =>$contactperson,
                'CustomerAddress1' =>$address1,
                'CustomerAddress2' =>$address2,
                'CustomerAddress3' =>$address3,
                'CustomerCode' =>$customercode,
                'CustomerName' =>$clientname,
                'CustomerPincode' =>$pincode,
                'CustomerTelephoneNumber' =>$telephone,
                'DoxNDox' =>$DoxNDox,
                'EmailID' =>$email,
                'MobileTelNo' =>$mobile,
                'NumberofPieces' =>$pieces,
                'OfficeCloseTime' =>$ofcclosingtime,
                'ProductCode' =>$productcode,
                'ReferenceNo' =>$refno,
                'Remarks' =>$remarks,
                'RouteCode' =>$routecode,
                'ShipmentPickupDate' =>$pickupdate,
                'ShipmentPickupTime' =>$pickupreadytime,
                'SubProducts' => $chktopay,
                'VolumeWeight' =>$volwt,
                'WeightofShipment' =>$actweight,
                'isToPayShipper' =>$chkval_shipper,
                'IsReversePickup' =>$IsReversePickup,
                'IsForcePickup' =>$IsForcePickup,
                'CISDDN' =>$IsDDN,
                'PackType' => $PackType
                ),
                'profile' => 
                array(
                'Api_type' => $apitype,
                'Area'=>$area,
                'Customercode'=>$customercode,
                'IsAdmin'=>'',
                'LicenceKey'=>$licencekey,
                'LoginID'=>$loginid,
                'Password'=>$password,
                'Version'=>$version
                )
            );

            //$result = $soap->__soapCall('RegisterPickup',array($params));
            $result = $soap->RegisterPickup($params);
            $res_error = $result->RegisterPickupResult->TokenNumber;
            $PickupDate = $result->RegisterPickupResult->ShipmentPickupDate;
            /* echo "<pre>";print_r($result); */
            if($res_error != ''){
                //"<p><span><b>ShipmentPickupDate :</b></span><span class='error_msg'>".$PickupDate."</span></p>";
                echo "
                <div class='divTable'>
                <div class='headRow'>
                <div class='divCell pincodes' align='center'>
                <p><span><b>Pickup Registraion</b></span></p>
                <p><span><b>IsError :</b></span><span>False</span></p>
                <p><span><b>Token No. :</b></span><span><b>" . $res_error . "</b></span></p>
                <p><span><b>Success Message :</b></span><span>Token Generated successfully</span></p>
                </div>
                </div>
                </div>";
                //echo "Token Generated successfully ".$result->RegisterPickupResult->TokenNumber;
            }else{
                $error = '';
                $count = count($result->RegisterPickupResult->Status->ResponseStatus);
                error_log('count'.$count.'------------------>');
                echo "
                <div class='divTable'>
                <div class='headRow'>
                <div class='divCell pincodes' align='center'>
                <p><span><b>Pickup Registraion</b></span></p>
                <p><span><b>IsError :</b></span><span class='error_msg'>True</span></p>
                <p><span><b>Error Message :</b></span><span class='error_msg'>";
                if($count>1){
                    for($i=0;$i<$count;$i++){
                    // echo "<span class='err_info'><span>". $result->RegisterPickupResult->Status->ResponseStatus[$i]->StatusCode."</span>";
                    echo "<span class='err_info'><span>".$result->RegisterPickupResult->Status->ResponseStatus[$i]->StatusInformation."</span></span>";
                    }
                }else{
                    echo $result->RegisterPickupResult->Status->ResponseStatus->StatusInformation;
                }
                echo "</span></div></div></div>";
            }
            exit;
        }catch (Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
    }   
}
