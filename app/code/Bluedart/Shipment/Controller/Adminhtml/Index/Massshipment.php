<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Massshipment extends \Magento\Backend\App\Action
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
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->shipmentLoader = $shipmentLoader;
        $this->trackFactory = $trackFactory;
        $this->_request = $context->getRequest();
        $this->resultJsonFactory = $resultJsonFactory;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/bluedart_massAWBgeneration.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
        $this->logger->info("__construct Massshipment");

        parent::__construct($context);
    }

    public function execute()
    {
        //echo "string";exit();
        $post_out = $this->getRequest()->getPost();
        $response = '';
        if (count($post_out["selectedOrders"])) {
            foreach ($post_out["selectedOrders"] as $key => $order_id) {
				
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();				
				$order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($order_id);
				
				$itemsv = $order->getAllVisibleItems();
				
		    //$cFlag = array("A","B","C","D","E","F","G","H","I","J","K");
		 
		 
			$counter = 0;
		    foreach($itemsv as $itemvv){
           
            if($itemvv->getQtyOrdered()==$itemvv->getQtyShipped()){
                $isShipped = true;
                continue;
            }else{
			     $cFlag = substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 2);
				 $output = $this->ShipmentCreation($order_id,$itemvv,$cFlag);//print_r($output);
				 $counter++;
				 
                if ($output[1] == "error") {
                    $response .= "<p class='bluedart_red'> Shipment for order " . $order_id . ' not created.<br>Error: '.$output[0].'</p>';
                } else {
                    $response .= "<p class='bluedart_green'> Shipment for order " . $order_id . ' has been created.<br>Success: '.$output[0].'</p>';
					
					$itemvv->setQtyShipped($itemvv->getQtyOrdered());
					$itemvv->save();
					
                }
				
			}
			}
			$order->setIsInProcess(true);
			$order->save();
			
				
               
            }
            return  $this->resultJsonFactory->create()->setData(['Response' => $response]);
        }
    }

    protected function ShipmentCreation($order_id,$itemvv,$cFlag){
    	$post = $this->getRequest()->getPost();
        $params = array();
        parse_str($post['str'], $params);//echo "<pre>";print_r($params);
        //echo "<pre>";print_r($post);exit();
        // Store Config Data
        $storeScope     = ScopeInterface::SCOPE_STORE;
        $apitype = $this->scopeConfig->getValue('bluedart/settings/api_type1', $storeScope);
        $area = $this->scopeConfig->getValue('bluedart/settings/area', $storeScope);
        $customercode = $this->scopeConfig->getValue('bluedart/settings/customercode', $storeScope);
        $licencekey = $this->scopeConfig->getValue('bluedart/settings/licencekey', $storeScope);
        $loginid = $this->scopeConfig->getValue('bluedart/settings/loginid', $storeScope);
        $password = $this->scopeConfig->getValue('bluedart/settings/password', $storeScope);
        $version = $this->scopeConfig->getValue('bluedart/settings/version', $storeScope);
        // Store Config Data

    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $post['bluedart_shipment_original_reference'] = $order_id;
        $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($post['bluedart_shipment_original_reference']);
        $errors = '';
		

    	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$ispickupcreate = $this->getRequest()->getParam('bluedart_is_pickup_require');  		
        
        $chktopay = $this->getRequest()->getParam('chktopay');
        if($chktopay == 'chktopay') {
          $chktopay_fin = true;
        } else {
          $chktopay_fin = false;
        }

        $storeScope         = ScopeInterface::SCOPE_STORE;
        $shipper_area       = $this->scopeConfig->getValue('bluedart/settings/area', $storeScope);
        $shipper_clientname = $this->scopeConfig->getValue('bluedart/settings/client_name', $storeScope);
        $shipper_address1   = $this->scopeConfig->getValue('bluedart/settings/client_address1', $storeScope);
        $shipper_address2   = $this->scopeConfig->getValue('bluedart/settings/client_address2', $storeScope);
        $shipper_address3   = $this->scopeConfig->getValue('bluedart/settings/client_address3', $storeScope);
        $shipper_pincode    = $this->scopeConfig->getValue('bluedart/settings/client_pincode', $storeScope);
        $shipper_mobile     = $this->scopeConfig->getValue('bluedart/settings/client_mobile', $storeScope);
        $shipper_telephone  = $this->scopeConfig->getValue('bluedart/settings/client_telephone', $storeScope);
        $shipper_sender     = $this->scopeConfig->getValue('bluedart/settings/client_name', $storeScope);
        $shipper_email      = $this->scopeConfig->getValue('bluedart/settings/client_email', $storeScope);
        $shipper_customercode = $this->scopeConfig->getValue('bluedart/settings/customercode', $storeScope);
        $shipper_vendorcode = $this->scopeConfig->getValue('bluedart/settings/client_vendorcode', $storeScope);
        $shipper_latitude   = $this->scopeConfig->getValue('bluedart/settings/client_latitude', $storeScope);
        $shipper_longitude  = $this->scopeConfig->getValue('bluedart/settings/client_longitude', $storeScope);
        $shipper_addressinfo = $this->scopeConfig->getValue('bluedart/settings/client_addressinfo', $storeScope);

        $shipping          = $order->getShippingAddress();
        $street            = $shipping->getData('street');
        $city              = $shipping->getData('city');                
        $region            = $shipping->getData('region');
        $street_length     = strlen($street);   
        $street_addr1      = substr($street, 0, 30); 
        $street_addr2      = substr($street, 30, 30);
        $street_addr3      = substr($street, 60, $street_length);

        $consignee_adress1 = $street_addr1;
        $consignee_adress2 = $street_addr2;
        $consignee_adress3 = $street_addr3.", ".$city . ", " .$region;
        $consignee_name    = $shipping->getData('firstname').' '.$shipping->getData('lastname');
        $consignee_pincode = $shipping->getData('postcode');
        $consignee_mobile  = $shipping->getData('telephone');
        $consignee_telephone = $shipping->getData('telephone');
        $consignee_attention = $shipping->getData('firstname').' '.$shipping->getData('lastname');
        $consignee_emailid   = $this->getRequest()->getParam('email');
        $consignee_latitude  = null;
        $consignee_longitude = null;
        $consignee_addressinfo = $consignee_adress1 .' '. $consignee_adress3;
        $consignee_countrycode = $shipping->getData('country_id');
        $consignee_statecode   = $shipping->getData('region_id');

        $itemsv = $order->getAllVisibleItems();
        $totalOrderedItems = $order->getTotalQtyOrdered();
        $numOfShippedItems = 0;
        $totalWeight  = 0;
        $itemscount   = 0;
        $isShipped    = false;
        $_qty         = 0;
        $tot_price    = 0;
        $main_price   = 0;
        $qty_counter  = 0;
        $bluedart_items = array();
		//$CommodityDetail = array("CommodityDetail1"=>"","CommodityDetail2"=>"","CommodityDetail3"=>"");
		
      //  foreach($itemsv as $itemvv){
      //      $numOfShippedItems += $itemvv->getQtyShipped();
      //      if($itemvv->getQtyOrdered()==$itemvv->getQtyShipped() && $qty_counter >= 3){
      //          $isShipped = true;
      //          continue;
      //      }else{
               $qty_counter++;
				//$itemscount =1;
                if($itemvv->getQtyOrdered() > $itemvv->getQtyShipped()){
                    $_qty = abs($itemvv->getQtyOrdered() - $itemvv->getQtyShipped());
                    //$itemscount += $_qty;
					$itemscount = $_qty;
                    $tot_price = $itemvv->getBasePrice() * $_qty;
                    $main_price += $tot_price;
                    if($itemvv->getWeight() != 0){
                        $totalWeight += $itemvv->getWeight() * $_qty;
                    } else {
                        $totalWeight += 0.5 * $_qty;
                    }
                    $commodity_name[$qty_counter] = $itemvv->getName();
                }
				
				//$CommodityDetail["CommodityDetail".$qty_counter] = str_replace(" ","",$itemvv->getName());
                // collect items for bluedart
                $ProductDesc1 = $itemvv->getId() . ' - ' . trim($itemvv->getName());
                $bluedart_items['ItemDetails'][] = array(
                    'HSCode' => '',
                    'Instruction' => '',
                    'ItemID' => $itemvv->getId(),
                    'ItemName' => $itemvv->getName(),
                    'Itemquantity' => $_qty,
                    'ItemValue' => sprintf("%.2f", $tot_price),
                    'ProductDesc1' => $ProductDesc1,
                    'SKUNumber' => $itemvv->getSku(),
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
        //    }
      //  }

       // if($numOfShippedItems == $totalOrderedItems){
      //      $errors = $errors.'Cannot do shipment as shipment is created for all items.';
      //      return (array($errors, 'error'));
      //  }

        if($order->getPayment()->getMethodInstance()->getCode() == 'cashondelivery'){
            $SubProductCode = 'C';
            $CollectableAmount = $main_price;
        }
        else{
            $SubProductCode = 'P';
            $CollectableAmount = 0.00;
        }
        //print_r($bluedart_items);exit();
        $service_pieces = $itemscount;
        $service_productcode = strtoupper($params['service_productcode']);
        $service_subproduct = $SubProductCode;
        $service_actweight = $totalWeight;
        $service_invoiceno = null;
        $service_dox = $params['dox'];
        $service_packtype = $params['service_packtype'];
        $service_creaditrefno = $order->getIncrementId();
        $service_spclinstruction = null;
        $service_pickupdate = $params['service_pickupdate'];
        $service_pickupreadytime = $params['service_pickupreadytime'];

        $service_declaredval = $main_price;
        $service_colamount = $CollectableAmount;
        //$service_waybillnumber = $this->getRequest()->getParam('service_waybillnumber');
        $service_cmdtydetail1 = isset($commodity_name[1]) ? $commodity_name[1] : '';
        $service_cmdtydetail2 = isset($commodity_name[2]) ? $commodity_name[2] : '';
        $service_cmdtydetail3 = isset($commodity_name[3]) ? $commodity_name[3] : '';
        $service_dimentionsl = 1;
        $service_dimentionsb = 1;
        $service_dimentionsh = 1;
        $service_count = $itemscount;

        //$service_awbnumber = $this->getRequest()->getParam('service_awbnumber');
        $service_deliverytimeslot = '';
        $service_parcelshopcode = '';
        $service_customerEDD = $params['service_customerEDD'];
        $service_creaditrefno2 = '';
        $service_creaditrefno3 = '';
        $service_pickuptype = $params['service_pickuptype'];
        $service_itemcount = $itemscount;
        $service_totalcashpaytocustomer = 0.0;
        $service_preferredpickuptimeslot = $params['service_preferredpickuptimeslot'];
        $service_deferreddeliverydays = 0;//$this->getRequest()->getParam('service_deferreddeliverydays');
        $service_officecutofftime = 1800;//$this->getRequest()->getParam('service_officecutofftime');
        $service_ispartialpickup = false;//$this->getRequest()->getParam('service_ispartialpickup');

        $service_pickupmode = $params['service_pickupmode'];
        //$service_isDDN = $this->getRequest()->getParam('service_isDDN');
        $service_registerpickup = $params['service_registerpickup'];
        $service_isforcepickup = $params['service_isforcepickup'];
        $PDFOutputNotRequired = false;
        
		$pickupurl = $this->getUrl("shipment/index/pickupregistration",array('order_id'=>$order->getId()));
		$url = $this->getUrl("sales/order/index",array('order_id'=>$order->getId()));
   
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
		      	array('AWBNo' => ''),
		        'ActualWeight' => sprintf("%.2f", $service_actweight),
		        'CollectableAmount' => sprintf("%.2f", $service_colamount),
		        'Commodity' => array (
		            'CommodityDetail1'  => $service_cmdtydetail1,
		            'CommodityDetail2' => $service_cmdtydetail2,
		            'CommodityDetail3' => $service_cmdtydetail3
		        ),
		        'CreditReferenceNo' => $service_creaditrefno."".$cFlag,
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
		          /*'ForwardAWBNo' => '',//service_forwardAWBNo
		          'ForwardLogisticCompName' => '',//$service_forwardlogisticcompname,*/
		          'InvoiceNo' => $service_invoiceno,
		          /*'IsDedicatedDeliveryNetwork' => $service_isDDN,
		          'IsDDN' => $service_isDDN,*/
		          'IsForcePickup' => $service_isforcepickup,
		          'IsPartialPickup' => $service_ispartialpickup,
		          /*'IsReversePickup' => 'false',//$service_isreversepickup,*/
		          'ItemCount' => 1,// count($itemsv),//$service_itemcount,
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
			/*$formSession = $objectManager->create('\Magento\Backend\Model\Session');
	        $formSession->setData("form_bluedartdata", $params);*/
	      // echo "<pre>";print_r($params);//exit;
            $this->logger->info("Massshipment Request params for Order: ".$order_id.PHP_EOL.print_r($params,1));
			$result = $soap->GenerateWayBill($params);//echo "<pre>";print_r($result);exit;
            $this->logger->info(PHP_EOL."Massshipment Response for Order: ".$order_id.PHP_EOL.print_r($result,1));
			$awbno = $result->GenerateWayBillResult->AWBNo;
            
			if($awbno){
				$DestinationArea = $result->GenerateWayBillResult->DestinationArea;
				$DestinationLocation = $result->GenerateWayBillResult->DestinationLocation;
				$CCRCRDREF = $result->GenerateWayBillResult->CCRCRDREF;
				$extra_details = $DestinationArea.":".$DestinationLocation.":".$CCRCRDREF;
				$awb_pdf = "";
				//if($PDFOutputNotRequired == false) {
					if($result->GenerateWayBillResult->AWBPrintContent) {
					  $awb_pdf =  $result->GenerateWayBillResult->AWBPrintContent;
					}
				//}

				if ($order->canShip()) {
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

                   // $this->shipmentLoader->setOrderId($post['bluedart_shipment_original_reference']); //4
				   //$this->shipmentLoader->setOrderId($order->getIncrementId());
                   // $this->shipmentLoader->setShipmentId(null); //null
                   // $this->shipmentLoader->setShipment(null); //array (size=2) 'items' =>  array (size=1)  4 => string '1' (length=1)  'comment_text' => string '' (length=0)
                  //  $this->shipmentLoader->setTracking(null); // null
                  //  $shipment = $this->shipmentLoader->load();
				  
					$convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
					$shipment = $convertOrder->toShipment($order);

					// Loop through order items
					//$vcounter = 0;
					//foreach ($order->getAllItems() AS $orderItem) {
					// Check if order item has qty to ship or is virtual
					//if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual() && $vcounter >=3) {
				//	continue;
				//	}
				//	$vcounter++;

					$qtyShipped = $itemvv->getQtyToShip();
					
					//$itemvv->setQtyShipped($qtyShipped);
					//$itemvv->save();

					// Create shipment item with qty
					$shipmentItem = $convertOrder->itemToShipmentItem($itemvv)->setQty($qtyShipped);

					// Add shipment item to shipment
					$shipment->addItem($shipmentItem);
					//}



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
					$shipment->getOrder()->setIsInProcess(true);
                    //$this->_saveShipment($shipment);  
                    $shipment->save();
                    $shipment->getOrder()->save();					

					if($awb_pdf != "") {
						$this->generatepdf($awb_pdf,$awbno);
						$awfile = BP."/var/bluedart/".$awbno.".pdf";
                        if (!file_exists($awfile)) {
                            $this->_messageManager->addError("AWB Pdf not generated. Please check the permission for var/bluedart/ folder.");
                        }
						/*if (file_exists($awfile)) {
							$this->_messageManager->addSuccess('AWB Pdf generated succesfully.');
						} else {
							$this->_messageManager->addError("AWB Pdf not generated. Please check the permission for var/bluedart/ folder.");
						}*/
					}
                    
					//$this->_messageManager->addSuccess('Blue Dart Shipment Number: '.$shipment->getIncrementId().' With AWB Number: '.$awbno.' has been created.');
                    $errors = 'Blue Dart Shipment Number: '.$shipment->getIncrementId().' With AWB Number: '.$awbno.' has been created.';
                    return (array($errors, 'success'));
					//error_log('before redirect--->'.$url);
					/*if($pickupurl && $ispickupcreate){
						$this->_redirect($pickupurl);return;
					}else{
						$this->_redirect($url);return;
					}*/
				} else{
					//$this->_messageManager->addError('Cannot do shipment for the order.');
                    $errors = 'Cannot do shipment for the order';
                    return (array($errors, 'error'));
				}
			}else{
				//echo  $result->GenerateWayBillResult->Status->WayBillGenerationStatus[0]->StatusCode;
				$count = count($result->GenerateWayBillResult->Status->WayBillGenerationStatus);
				if($count>1){
					for($i=0;$i<$count;$i++){
					$errors = $errors."<br>".$result->GenerateWayBillResult->Status->WayBillGenerationStatus[$i]->StatusInformation;
					}
				}else{
					$errors = $result->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation;
				}
				/*$this->_messageManager->addError('Error: '.$error);
				$this->_redirect($url);return;*/
                return (array($errors, 'error'));
			}
		}catch(Exception $e){
			/*$this->_messageManager->addError($e->getMessage());
			$this->_redirect($url);return;*/
            $errors = $e->getMessage();
            return array($errors, 'error');
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
