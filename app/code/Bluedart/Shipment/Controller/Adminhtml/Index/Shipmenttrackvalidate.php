<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Shipmenttrackvalidate extends \Magento\Backend\App\Action
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
        $this->Shipmenttrack();
    }

    public function Shipmenttrack (){
        $waybillnumber = $this->getRequest()->getParam('waybillnumber');

        // Store Config Data
        $storeScope     = ScopeInterface::SCOPE_STORE;
        $apitype        = $this->scopeConfig->getValue('bluedart/settings/api_type', $storeScope);
        $area           = $this->scopeConfig->getValue('bluedart/settings/area', $storeScope);
        $customercode   = $this->scopeConfig->getValue('bluedart/settings/customercode', $storeScope);
        $licencekey     = $this->scopeConfig->getValue('bluedart/settings/licencekey_fortracking', $storeScope);
        $loginid        = $this->scopeConfig->getValue('bluedart/settings/loginid', $storeScope);
        $password       = $this->scopeConfig->getValue('bluedart/settings/password', $storeScope);
        $version        = $this->scopeConfig->getValue('bluedart/settings/version', $storeScope);

        try{
            // Get cURL resource
            $curl = curl_init();
            // Set some options - we are passing in a useragent too here
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => "http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=".$loginid."&awb=awb&numbers=".$waybillnumber."&format=XML&lickey=".$licencekey."&verno=".$version."&scan=1",
                CURLOPT_USERAGENT => "Codular Sample cURL Request"
            ));
            // Send the request & save response to $resp
            $result = curl_exec($curl);
            // Close request to clear up some resources
            curl_close($curl);

            $result = simplexml_load_string($result);
            
            /* echo "<pre>"; print_r($result);*/
                
            if($result->Shipment->StatusType == 'NF') {
				
				//echo "testt".$result->Shipment->StatusType;exit;
                $res_error_type = "NF";
                $res_error = "Incorrect Waybill number or No Information";
                if($result->Shipment->StatusType) {
                    $res_error_type = $result->Shipment->StatusType;
                }
                if($result->Shipment->Status) {
                    $res_error = $result->Shipment->Status;
                }
                echo "
                  <div class='divTable'>
                  <div class='headRow'>
                  <div class='divCell pincodes' align='center'>
                  <p><span><b>Shipment Tracking </b></span></p>
                  <p><span><b>Status Type </b></span><span class='error_msg'>".$res_error_type."</span></p>
                  <p><span><b>Description </b></span><span class='error_msg'>".$res_error."</span></p>
                  </div>
                  </div>
                  </div>";
            } else {
                echo "
                    <div class='divTable'>
                    <div class='headRow'>
                    <div class='divCell pincodes' align='center'>
                    <p><span><b>Shipment Tracking</b></span></p>
                    <!--<p><span><b>Product Code </b></span><span>".$result->Shipment->Prodcode."</span></p>
                    <p><span><b>Service </b></span><span>".$result->Shipment->Service."</span></p>-->
                    <p><span><b>Pick Up Date </b></span><span>".$result->Shipment->PickUpDate."</span></p>
                    <p><span><b>Pick Up Time </b></span><span>".$result->Shipment->PickUpTime."</span></p>
                    <p><span><b>Origin </b></span><span>".$result->Shipment->Origin."</span></p>
                    <!--<p><span><b>Origin Area Code </b></span><span>".$result->Shipment->OriginAreaCode."</span></p>-->
                    <p><span><b>Destination </b></span><span>".$result->Shipment->Destination."</span></p>
                    <!--<p><span><b>Destination Area Code </b></span><span>".$result->Shipment->DestinationAreaCode."</span></p>
                    <p><span><b>Product Type </b></span><span>".$result->Shipment->ProductType."</span></p>
                    <p><span><b>Customer Code </b></span><span>".$result->Shipment->CustomerCode."</span></p>
                    <p><span><b>Customer Name </b></span><span>".$result->Shipment->CustomerName."</span></p>
                    <p><span><b>Sender Name </b></span><span>".$result->Shipment->SenderName."</span></p>
                    <p><span><b>ToAttention </b></span><span>".$result->Shipment->ToAttention."</span></p>
                    <p><span><b>Consignee </b></span><span>".$result->Shipment->Consignee."</span></p>
                    <p><span><b>Consignee Address 1 </b></span><span>".$result->Shipment->ConsigneeAddress1."</span></p>
                    <p><span><b>Consignee Address 2 </b></span><span>".$result->Shipment->ConsigneeAddress2."</span></p>
                    <p><span><b>Consignee Address 3 </b></span><span>".$result->Shipment->ConsigneeAddress3."</span></p>
                    <p><span><b>Consignee Pincode </b></span><span>".$result->Shipment->ConsigneePincode."</span></p>
                    <p><span><b>Weight </b></span><span>".$result->Shipment->Weight."</span></p>-->
                    <p><span><b>Status </b></span><span>".$result->Shipment->Status."</span></p>
                    <!--<p><span><b>Status Type </b></span><span>".$result->Shipment->StatusType."</span></p>
                    <p><span><b>Expected Delivery Date </b></span><span>".$result->Shipment->ExpectedDeliveryDate."</span></p>-->
                    <p><span><b>Status Date </b></span><span>".$result->Shipment->StatusDate."</span></p>
                    <p><span><b>Status Time </b></span><span>".$result->Shipment->StatusTime."</span></p>
                    <p><span><b>Received By </b></span><span>".$result->Shipment->ReceivedBy."</span></p>
                    <!--<p><span><b>Instructions </b></span><span>".$result->Shipment->Instructions."</span></p>-->
                    </div>
                    </div>
                    </div>
                ";
                        
                $count_scan = count($result->Shipment->Scans->ScanDetail);
                $scan_details = $result->Shipment->Scans->ScanDetail;
                $scanhtml = "";
                $r =0;

                /* foreach($scan_details as $value){
                    $r++;
                    $scanhtml .= "<div class='divTable shipment_tracking'>
                <div class='headRow'>
                  <div class='divCell pincodes' align='center'>
                  <p><span><b>Scan Detail ".$r." </b></span><span><b>Description</b></span></p>
                  ";
                    $scanhtml .= "<p><span><b>Scan</b></span><span>". $value->Scan ."</span></p>";
                    $scanhtml .= "<p><span><b>Scan Code</b></span><span>". $value->ScanCode ."</span></p>";
                    $scanhtml .= "<p><span><b>Scan Type</b></span><span>". $value->ScanType ."</span></p>";
                    $scanhtml .= "<p><span><b>Scan Group Type</b></span><span>". $value->ScanGroupType ."</span></p>";
                    $scanhtml .= "<p><span><b>Scan Date</b></span><span>". $value->ScanDate ."</span></p>";
                    $scanhtml .= "<p><span><b>Scan Time</b></span><span>". $value->ScanTime ."</span></p>";
                    $scanhtml .= "<p><span><b>Scanned Location</b></span><span>". $value->ScannedLocation ."</span></p>";
                    $scanhtml .= "<p><span><b>Scanned Location Code</b></span><span>". $value->ScannedLocationCode ."</span></p>";
                    $scanhtml .= "<p><span></span><span></span></p>
                              </div>
                            </div>
                    </div>";
                } */
                        
                $scanhtml .= "<div class='divTable shipment_tracking'>
                <div class='headRow'>
                  <div class='divCell pincodes' align='center'>
                  <p><span><b>Status and Scans</b></span></p>
                  <p>
                      <span><b>Location</b></span>
                      <span><b>Details</b></span>
                      <span><b>Date</b></span>
                      <span><b>Time</b><span class='time_required'><b>*</b></span></span>
                  </p>
                  <p><span class='waybillnum'><b>Waybill No : " . $waybillnumber . "</b></span></p>
                  ";
                foreach($scan_details as $value){
                    $r++;
                    $scanhtml .= "<p>
                                    <span>". $value->ScannedLocation ."</span>
                                    <span>". $value->Scan ."</span>
                                    <span>". $value->ScanDate ."</span>
                                    <span>". $value->ScanTime ."</span>
                                </p>";
                }
                $scanhtml .= "<p class='hr_format_p'>
                            <span class='hr_format_desc'><b>- 24 Hr Format</b></span>
                            <span class='hr_format'><b>*</b></span>
                            </p> </div> </div>
                    </div>";
                echo $scanhtml;
            }
            exit();      
        }catch (Exception $e) {
            $this->_messageManager->addError($e->getMessage());
            echo $e->getMessage();exit();
        }
    }
}
