<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Shipment extends \Magento\Backend\App\Action
{
	/**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $request;
    protected $scopeConfig;
    protected $_messageManager;
    protected $shipmentLoader;
    protected $trackFactory;
    protected $_request;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory
    ) {
        $this->_messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->shipmentLoader = $shipmentLoader;
        $this->trackFactory = $trackFactory;
        $this->_request = $context->getRequest();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/bluedart_AWBgeneration.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
        $this->logger->info("__construct Shipment");

        parent::__construct($context);
    }

    public function execute()
    {
        $this->ShipmentCreation();
    }

    protected function ShipmentCreation(){
    	$post = $this->getRequest()->getPost();
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$ispickupcreate = $this->getRequest()->getParam('bluedart_is_pickup_require');  		
        $shipper_area = $this->getRequest()->getParam('shipper_area');
        $shipper_customercode = $this->getRequest()->getParam('shipper_customercode');
        
        $chktopay = $this->getRequest()->getParam('chktopay');
        if($chktopay == 'chktopay') {
          $chktopay_fin = true;
        } else {
          $chktopay_fin = false;
        }

        $shipper_clientname = $this->getRequest()->getParam('shipper_clientname');
        $shipper_address1 = $this->getRequest()->getParam('shipper_address1');
        $shipper_address2 = $this->getRequest()->getParam('shipper_address2');
        $shipper_address3 = $this->getRequest()->getParam('shipper_address3');
        $shipper_pincode = $this->getRequest()->getParam('shipper_pincode');
        $shipper_mobile = $this->getRequest()->getParam('shipper_mobile');
        $shipper_telephone = $this->getRequest()->getParam('shipper_telephone');
        $shipper_sender = $this->getRequest()->getParam('shipper_sender');
        $shipper_email = $this->getRequest()->getParam('shipper_email');
        $shipper_vendorcode = $this->getRequest()->getParam('shipper_vendorcode');
        $shipper_latitude = $this->getRequest()->getParam('shipper_latitude');
        $shipper_longitude = $this->getRequest()->getParam('shipper_longitude');
        $shipper_addressinfo = $this->getRequest()->getParam('shipper_addressinfo');
        $consignee_name = $this->getRequest()->getParam('consignee_name');
        $consignee_adress1 = $this->getRequest()->getParam('consignee_adress1');
        $consignee_adress2 = $this->getRequest()->getParam('consignee_adress2');
        $consignee_adress3 = $this->getRequest()->getParam('consignee_adress3');
        $consignee_pincode = $this->getRequest()->getParam('consignee_pincode');
        $consignee_mobile = $this->getRequest()->getParam('consignee_mobile');
        $consignee_telephone = $this->getRequest()->getParam('consignee_telephone');
        $consignee_attention = $this->getRequest()->getParam('consignee_attention');
        $consignee_emailid = $this->getRequest()->getParam('consignee_emailid');
        $consignee_latitude = $this->getRequest()->getParam('consignee_latitude');
        $consignee_longitude = $this->getRequest()->getParam('consignee_longitude');
        $consignee_addressinfo = $this->getRequest()->getParam('consignee_addressinfo');
        $consignee_countrycode = $this->getRequest()->getParam('consignee_countrycode');
        $consignee_statecode = $this->getRequest()->getParam('consignee_statecode');

        $service_pieces = $this->getRequest()->getParam('service_pieces');
        $service_productcode = strtoupper($this->getRequest()->getParam('service_productcode'));
        $service_subproduct = strtoupper($this->getRequest()->getParam('service_subproduct'));
        $service_actweight = $this->getRequest()->getParam('service_actweight');
        $service_invoiceno = $this->getRequest()->getParam('service_invoiceno');
        $service_dox = $this->getRequest()->getParam('dox');
        
        $service_packtype = $this->getRequest()->getParam('service_packtype');
        $service_creaditrefno = $this->getRequest()->getParam('service_creaditrefno');
        $service_spclinstruction = $this->getRequest()->getParam('service_spclinstruction');
        $service_pickupdate = $this->getRequest()->getParam('service_pickupdate');
        $service_pickupreadytime = $this->getRequest()->getParam('service_pickupreadytime');
        $service_declaredval = $this->getRequest()->getParam('service_declaredval');
        $service_colamount = $this->getRequest()->getParam('service_colamount');
        //$service_waybillnumber = $this->getRequest()->getParam('service_waybillnumber');
        $service_cmdtydetail1 = $this->getRequest()->getParam('service_cmdtydetail1');
        $service_cmdtydetail2 = $this->getRequest()->getParam('service_cmdtydetail2');
        $service_cmdtydetail3 = $this->getRequest()->getParam('service_cmdtydetail3');
        $service_dimentionsl = $this->getRequest()->getParam('service_dimentionsl');
        $service_dimentionsb = $this->getRequest()->getParam('service_dimentionsb');
        $service_dimentionsh = $this->getRequest()->getParam('service_dimentionsh');
        $service_count = $this->getRequest()->getParam('service_count');

        $service_awbnumber = $this->getRequest()->getParam('service_awbnumber');
        $service_deliverytimeslot = $this->getRequest()->getParam('service_deliverytimeslot');
        $service_parcelshopcode = $this->getRequest()->getParam('service_parcelshopcode');
        $service_customerEDD = $this->getRequest()->getParam('service_customerEDD');
        $service_creaditrefno2 = $this->getRequest()->getParam('service_creaditrefno2');
        $service_creaditrefno3 = $this->getRequest()->getParam('service_creaditrefno3');
        $service_pickuptype = $this->getRequest()->getParam('service_pickuptype');
        $service_itemcount = $this->getRequest()->getParam('service_itemcount');
        $service_totalcashpaytocustomer = $this->getRequest()->getParam('service_totalcashpaytocustomer');
        $service_preferredpickuptimeslot = $this->getRequest()->getParam('service_preferredpickuptimeslot');
        $service_deferreddeliverydays = $this->getRequest()->getParam('service_deferreddeliverydays');
        $service_officecutofftime = $this->getRequest()->getParam('service_officecutofftime');
        $service_ispartialpickup = $this->getRequest()->getParam('service_ispartialpickup');
        $service_pickupmode = $this->getRequest()->getParam('service_pickupmode');
        $service_isDDN = $this->getRequest()->getParam('service_isDDN');
        $service_registerpickup = $this->getRequest()->getParam('service_registerpickup');
        $service_isforcepickup = $this->getRequest()->getParam('service_isforcepickup');

        $PDFOutputNotRequired = false;
        $PDFOutputNotRequired = $this->getRequest()->getParam('PDFOutputNotRequired');
       
        /*for($a=1;$a<=$this->getRequest()->getParam('counter_val');$a++){
          $product_id = $this->getRequest()->getParam('bluedart_item_id_' . $a);
          $bluedartIitemsQty[$product_id] = $this->getRequest()->getParam('bluedart_total_items_' . $product_id);
        }*/

        $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($post['bluedart_shipment_original_reference']);
		
		//echo $post['bluedart_shipment_original_reference'];exit;

        // Store Config Data
        $storeScope     = ScopeInterface::SCOPE_STORE;
		$apitype = $this->scopeConfig->getValue('bluedart/settings/api_type1');
		$area = $this->scopeConfig->getValue('bluedart/settings/area');
		$customercode = $this->scopeConfig->getValue('bluedart/settings/customercode');
		$licencekey = $this->scopeConfig->getValue('bluedart/settings/licencekey');
		$loginid = $this->scopeConfig->getValue('bluedart/settings/loginid');
		$password = $this->scopeConfig->getValue('bluedart/settings/password');
		$version = $this->scopeConfig->getValue('bluedart/settings/version');
		// Store Config Data

        $itemsv = $order->getAllVisibleItems();
        $prod_qty = array();
        foreach($itemsv as $item){
        	$prod_qty[$item->getId()] = $this->getRequest()->getParam('bluedart_input_items_qty_'.$item->getId());
        }
		
        foreach ($post['bluedart_items'] as $key => $value) {
        	if ($value != 0) {
        		//itrating order items
                foreach ($itemsv as $item) {
                    if ($item->getId() == $key) {
                    	// collect items for bluedart
                    	$ProductDesc1 = $item->getId() . ' - ' . trim($item->getName());
                        $bluedart_items['ItemDetails'][] = array(
                        	'HSCode' => '',
                        	'Instruction' => '',
                            'ItemID' => $item->getId(),
                            'ItemName' => $item->getName(),
                            'Itemquantity' => $value,
                            'ItemValue' => sprintf("%.2f", $post["bluedart_items_base_price_" . $item->getId()]),
                            'ProductDesc1' => $ProductDesc1,
                            'SKUNumber' => $item->getSku(),
                            'ProductDesc2' => '',
                        	'ReturnReason' => '',
                        	'SubProduct1' => '',
                        	'SubProduct2' => '',
                        	'SubProduct3' => '',
                        	'SubProduct4' => '',
                        	'countryOfOrigin' => '',
							 'InvoiceDate' => date("Y-m-d", strtotime($order->getUpdatedAt())),
				    'InvoiceNumber' => '',
				    'PlaceofSupply' => '',
					'ReturnReason' => ''
                        );
                    }
                }
        	}
        }
        //print_r($bluedart_items);exit();

		$pickupurl = $this->getUrl("shipment/index/pickupregistration",array('order_id'=>$order->getId()));
		$url = $this->getRequest()->getParam('bluedart_shipment_referer');
   
        $waybillgenerationurl = $this->scopeConfig->getValue('bluedart/url/waybillgeneration');
    	if(!$waybillgenerationurl){
			$this->messageManager->addError("Please enter waybill generation URL from system configuration.");
			/*$resultRedirect->setUrl($url);
			return $resultRedirect;*/
			$this->_redirect($url);return;
	  	}
    	$soap = new \Zend\Soap\Client($waybillgenerationurl.'?wsdl');
        $soap->setSoapVersion(SOAP_1_1);

		$params = array(
			'Request' => 
		  	array (
		    'Consignee' =>
		      array (
		        'ConsigneeAddress1' => $consignee_adress1,
		        'ConsigneeAddress2' => $consignee_adress2,
		        'ConsigneeAddress3' => $consignee_adress3,
		        'ConsigneeAttention'=> $consignee_attention,
		        'ConsigneeMobile'   => $consignee_mobile,
		        'ConsigneeName'     => $consignee_name,
		        'ConsigneePincode'  => $consignee_pincode,
		        'ConsigneeTelephone'=> $consignee_telephone,
		        'ConsigneeAddressinfo' => $consignee_addressinfo,
		        'ConsigneeCountryCode' => $consignee_countrycode,
		        'ConsigneeEmailID' 	   => $consignee_emailid,
		        'ConsigneeLatitude'    => $consignee_latitude,
		        'ConsigneeLongitude'   => $consignee_longitude,
		        'ConsigneeStateCode'   => $consignee_statecode
		      ) ,
		    'Returnadds' =>
		      array (
		        'ReturnAddress1' 	=> '',
		        'ReturnAddress2' 	=> '',
		        'ReturnAddress3' 	=> '',
		        'ReturnPincode'		=> '',
		        'ReturnTelephone'   => '',
		        'ReturnMobile'     	=> '',
		        'ReturnEmailID'  	=> '',
		        'ReturnContact'		=> '',
		        'ManifestNumber' 	=> '',
		        'ReturnLatitude' 	=> '',
		        'ReturnLongitude' 	=> '',
		        'ReturnAddressinfo' => ''
		      ) ,
		    'Services' => 
		      array (
		      	array('AWBNo' =>$service_awbnumber),
		        'ActualWeight' => sprintf("%.2f", $service_actweight),
		        'CollectableAmount' => sprintf("%.2f", $service_colamount),
		        'Commodity' =>
		          array (
		            'CommodityDetail1'  => $service_cmdtydetail1,
		            'CommodityDetail2' => $service_cmdtydetail2,
		            'CommodityDetail3' => $service_cmdtydetail3
		        ),
		        'CreditReferenceNo' => $service_creaditrefno,
		        'CreditReferenceNo2' => $service_creaditrefno2,
		        'CreditReferenceNo3' => $service_creaditrefno3,
		        'CustomerEDD' => $service_customerEDD,
		        'DeclaredValue' => sprintf("%.2f", $service_declaredval),
		        'DeferredDeliveryDays' => $service_deferreddeliverydays,
		        'DeliveryTimeSlot' => $service_deliverytimeslot,
		        'Dimensions' =>
		          array (
		            'Dimension' =>
		              array (
		                'Breadth' => $service_dimentionsb,
		                'Count' => $service_count,
		                'Height' => $service_dimentionsh,
		                'Length' => $service_dimentionsl
		              ),
		          ),
		          /*'ForwardAWBNo' => '',//$service_forwardAWBNo
		          'ForwardLogisticCompName' => '',//$service_forwardlogisticcompname,*/
		          'InvoiceNo' => $service_invoiceno,
		          /*'IsDedicatedDeliveryNetwork' => $service_isDDN,
		          'IsDDN' => $service_isDDN,*/
		          'IsForcePickup' => $service_isforcepickup,
		          'IsPartialPickup' => $service_ispartialpickup,
		          /*'IsReversePickup' => 'false',//$service_isreversepickup,*/
		          'ItemCount' => $service_itemcount,
		          'Officecutofftime' => $service_officecutofftime,
		          //'PDFOutputNotRequired' => $PDFOutputNotRequired,
		          'PDFPrintContent' => '',//$service_PDFprintcontent
		          'PackType' => $service_packtype,
		          'ParcelShopCode' => $service_parcelshopcode,
				  'PickupDate' => $service_pickupdate,
				  'PickupMode' => $service_pickupmode,
		          'PickupTime' => $service_pickupreadytime,
		          'PickupType' => $service_pickuptype,
		          'PieceCount' => $service_pieces,
		          'PreferredPickupTimeSlot' => $service_preferredpickuptimeslot,
		          'ProductCode' => $service_productcode,
		          'ProductType' => $service_dox,
		          'RegisterPickup' => $service_registerpickup,
		          'SpecialInstruction' => $service_spclinstruction,
		          'SubProductCode' => $service_subproduct,
		          //'service_waybillnumber' => $service_waybillnumber,
		          'TotalCashPaytoCustomer' => sprintf("%.2f", $service_totalcashpaytocustomer),
		          //'ItemDetails' => $bluedart_items,
		          'itemdtl' => $bluedart_items
		      ),
		      'Shipper' =>
		        array(
		          'CustomerAddress1' => $shipper_address1,
		          'CustomerAddress2' => $shipper_address2,
		          'CustomerAddress3' => $shipper_address3,
		          'CustomerCode' => $shipper_customercode,
		          'CustomerEmailID' => $shipper_email,
		          'CustomerMobile' => $shipper_mobile,
		          'CustomerName' => $shipper_clientname ,
		          'CustomerPincode' => $shipper_pincode,
		          'CustomerTelephone' => $shipper_telephone,
		          'IsToPayCustomer' =>  $chktopay_fin,
		          'OriginArea' => $shipper_area,
		          'Sender' => $shipper_sender,
		          'VendorCode' => $shipper_vendorcode,
		          'CustomerLatitude' => $shipper_latitude,
		          'CustomerLongitude' => $shipper_longitude,
		          'CustomerAddressinfo' => $shipper_addressinfo
		        )
		  ),
		  'Profile' => 
		     array(
		      'Api_type' => $apitype,
		      'LicenceKey'=>$licencekey,
		      'LoginID'=>$loginid,
		      'Version'=>$version)
		);

		// Here I call my external function
		try{
			$formSession = $objectManager->create('\Magento\Backend\Model\Session');
	        $formSession->setData("form_bluedartdata", $params);
	        //$this->_redirect($url);return;
            //echo "<pre>";print_r($params);//exit;
            $this->logger->info("Shipment Request params for Order: ".$post['bluedart_shipment_original_reference'].PHP_EOL.print_r($params,1));
			$result = $soap->GenerateWayBill($params);//echo "<pre>";print_r($result);exit;
            $this->logger->info(PHP_EOL."Shipment Response for Order: ".$post['bluedart_shipment_original_reference'].PHP_EOL.print_r($result,1));
			
			$awbno = $result->GenerateWayBillResult->AWBNo;

			if($awbno){
				$DestinationArea = $result->GenerateWayBillResult->DestinationArea;
				$DestinationLocation = $result->GenerateWayBillResult->DestinationLocation;
				$CCRCRDREF = $result->GenerateWayBillResult->CCRCRDREF;
				$extra_details = $DestinationArea.":".$DestinationLocation.":".$CCRCRDREF;
				$awb_pdf = "";
				if($PDFOutputNotRequired == "false") {
					if($result->GenerateWayBillResult->AWBPrintContent) {
					  $awb_pdf =  $result->GenerateWayBillResult->AWBPrintContent;
					}
				}

				if ($order->canShip() && $post['bluedart_return_shipment_creation_date'] == "create") {
					$data = [
                        'items' => $post['bluedart_items'],
                        'comment_text' => "AWB No. " . $awbno . " - Order No. " . $post['bluedart_shipment_original_reference'],
                        'comment_customer_notify' => true,
                        'is_visible_on_front' => true
                    ];

                    $track = array(
						'carrier_code' => 'bluedart',
						'title' => 'Bluedart',
						'number' => $awbno, // Replace with your tracking number
					);

                    $this->shipmentLoader->setOrderId($order->getId()); //4
                    $this->shipmentLoader->setShipmentId(null); //null
                    $this->shipmentLoader->setShipment(null); //array (size=2) 'items' =>  array (size=1)  4 => string '1' (length=1)  'comment_text' => string '' (length=0)
                    $this->shipmentLoader->setTracking(null); // null
                    $shipment = $this->shipmentLoader->load();

                    $shipment->addTrack(
		                $this->trackFactory->create()->addData($track)
		            );

                    if (!$shipment) {
                        $this->_forward('noroute');
                        return;
                    }

                    if (!empty($data['comment_text'])) {
                        $shipment->addComment(
                                $data['comment_text'], isset($data['comment_customer_notify']), isset($data['is_visible_on_front'])
                        );

                        $shipment->setCustomerNote($data['comment_text']);
                        $shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
                    }

					///////////// block shipment
                    $shipment->register();
                    $this->_saveShipment($shipment);     

					if($awb_pdf != "") {
						$this->generatepdf($awb_pdf,$awbno);
						$awfile = BP."/var/bluedart/".$awbno.".pdf";
						if (file_exists($awfile)) {
							$this->_messageManager->addSuccess('AWB Pdf generated succesfully.');
						} else {
							$this->_messageManager->addError("AWB Pdf not generated. Please check the permission for var/bluedart/ folder.");
						}
					}
                    $formSession->unsetData('form_bluedartdata');
					$this->_messageManager->addSuccess('Blue Dart Shipment Number: '.$shipment->getIncrementId().' With AWB Number: '.$awbno.' has been created.');
					//error_log('before redirect--->'.$url);
					if($pickupurl && $ispickupcreate){
						/*$resultRedirect->setUrl($pickupurl);
						return $resultRedirect;*/
						$this->_redirect($pickupurl);return;
					}else{
						/*$resultRedirect->setUrl($url);
						return $resultRedirect;*/
						$this->_redirect($url);return;
					}
				} else{
					$this->_messageManager->addError('Cannot do shipment for the order.');
				}
			}else{
				$error = '';
				//echo  $result->GenerateWayBillResult->Status->WayBillGenerationStatus[0]->StatusCode;
				$count = count($result->GenerateWayBillResult->Status->WayBillGenerationStatus);
				if($count>1){
					for($i=0;$i<$count;$i++){
					$error = $error."<br>".$result->GenerateWayBillResult->Status->WayBillGenerationStatus[$i]->StatusInformation;
					}
				}else{
					$error = $result->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation;
				}
				$this->_messageManager->addError('Error: '.$error);
				/*$resultRedirect->setUrl($url);
				return $resultRedirect;*/
				$this->_redirect($url);return;
			}
		}catch(Exception $e){
			$this->_messageManager->addError($e->getMessage());
			/*$resultRedirect->setUrl($url);
			return $resultRedirect;*/
			$this->_redirect($url);return;
	  	}
	  	exit;
    }

    protected function _saveShipment($shipment) {
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->_objectManager->create('Magento\Framework\DB\Transaction');
        $transaction->addObject(
                $shipment
        )->addObject(
                $shipment->getOrder()
        )->save();
        return $this;
    }

    public function generatepdf($awb_pdf,$awbno){
      	$file = fopen(BP."/var/bluedart/".$awbno.".pdf","w+");
      	fwrite($file,$awb_pdf);
      	fclose($file);
      	return;
    }
}
