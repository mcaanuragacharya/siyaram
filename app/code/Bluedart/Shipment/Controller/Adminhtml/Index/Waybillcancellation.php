<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Store\Model\ScopeInterface;

class Waybillcancellation extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    protected $scopeConfig;
    protected $storeScope;
    protected $apitype;
    //protected $area;
    //protected $customercode;
    protected $licencekey;
    protected $loginid;
    //protected $password;
    protected $version;
    protected $waybillgenerationurl;

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement   = $orderManagement;
        $this->scopeConfig    = $scopeConfig;
        $this->storeScope     = ScopeInterface::SCOPE_STORE;
        $this->apitype        = $this->scopeConfig->getValue('bluedart/settings/api_type1', $this->storeScope);
        //$this->area           = $this->scopeConfig->getValue('bluedart/settings/area', $this->storeScope);
        //$this->customercode   = $this->scopeConfig->getValue('bluedart/settings/customercode', $this->storeScope);
        $this->licencekey     = $this->scopeConfig->getValue('bluedart/settings/licencekey', $this->storeScope);
        $this->loginid        = $this->scopeConfig->getValue('bluedart/settings/loginid', $this->storeScope);
        //$this->password       = $this->scopeConfig->getValue('bluedart/settings/password', $this->storeScope);
        $this->version        = $this->scopeConfig->getValue('bluedart/settings/version', $this->storeScope);
        $this->waybillgenerationurl  = $this->scopeConfig->getValue('bluedart/url/waybillgeneration', $this->storeScope);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/bluedart_massAWBcancellation.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
        $this->logger->info("__construct Waybillcancellation".PHP_EOL);
    }

    /**
     * Generate Waybill Numbers
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        //$countGeneratedWaybill = 0;
        $success = '';$error = '';$output = array();

        if(!$this->waybillgenerationurl){
            $message = "Please enter Way Bill Generation URL from system configuration.";
            $this->messageManager->addError(__($message));
            $this->_redirect('sales/order/index');
        }
        
        foreach ($collection->getItems() as $order) {
            //echo '<pre>';print_r($order->getData());
            if (!$order->getEntityId()) {
                continue;
            }
            $output = $this->cancelWaybill($order);
            if ($output[1] == "error") {
                $error .= "<p class='bluedart_red'> Waybill for order " . $order->getIncrementId() . ' not cancelled.<br><b>Error: </b>'.$output[0].'</p>';
            } else {
                $order->addStatusHistoryComment($output[0]);
                $order->save();
                $success .= "<p class='bluedart_green'> Waybill for order " . $order->getIncrementId() . ' has been cancelled.<br><b>Success: </b>'.$output[0].'</p>';
            }
            //$countGeneratedWaybill++;
        }
        if ($success) {
            $this->messageManager->addSuccess(__($success));
        }
        if ($error) {
            $this->messageManager->addError(__($error));
        }
        
        /*$countNonGeneratedWaybill = $collection->count() - $countGeneratedWaybill;

        if ($countNonGeneratedWaybill && $countGeneratedWaybill) {
            $this->messageManager->addError(__('For %1 order(s) waybill cannot be generated.', $countNonGeneratedWaybill));
        } elseif ($countNonGeneratedWaybill) {
            $this->messageManager->addError(__('You cannot generate waybill for the selected order(s).'));
        }

        if ($countGeneratedWaybill) {
            $this->messageManager->addSuccess(__('We generated waybill for %1 order(s).', $countGeneratedWaybill));
        }*/
        $this->_redirect('sales/order/index');
    }

    /***********  Get latest one Tracking Number assigned to Shipment  **********/
    protected function cancelWaybill($order)
    {
        $message = '';$output = array();
        $trackNumbers = $order->getTracksCollection();
        $trackNumbers->getSelect()->order('entity_id desc')->limit(1);
        if(count($trackNumbers) > 0){
            $trackData =  $trackNumbers->getData();//echo '<pre>';print_r($trackData);
            if ($trackData[0]['carrier_code'] == 'bluedart' ) {
                $waybillnumber = $trackData[0]['track_number'];
                $this->logger->info("Tracking number ".$waybillnumber." found for the Order ".$order->getIncrementId().PHP_EOL);
                $output = $this->cancelWaybillNumber($waybillnumber);
                return $output;
            }else{
                $this->logger->info("There is no Bluedart Tracking number found for the Order ".$order->getIncrementId().PHP_EOL);
                $message = "There is no Bluedart Tracking number found for the Order ".$order->getIncrementId();
                return (array($message, 'error'));
            }
        }else{
            $this->logger->info("There is no Tracking number found for the Order ".$order->getIncrementId().PHP_EOL);
            $message = "There is no Tracking number found for the Order ".$order->getIncrementId();
            return (array($message, 'error'));
        }
    }

    public function cancelWaybillNumber($waybillnumber){
        try{
            $message = '';
            $soap = new \Zend\Soap\Client($this->waybillgenerationurl.'?wsdl');
            $soap->setSoapVersion(SOAP_1_1);

            $params = array('Request'=>
                        array('AWBNo' =>$waybillnumber),
                        'Profile' => 
                        array(
                            'Api_type' => $this->apitype,                          
                            'LicenceKey'=>$this->licencekey,
                            'LoginID'=>$this->loginid,
                            'Version'=>$this->version
                            )
                        );
            //echo '<h2>Result</h2><pre>'; print_r($result);exit;
            $result = $soap->CancelWaybill($params);
            //echo '<h2>Result</h2><pre>'; print_r($result);exit;
            $this->logger->info("CancelWaybill Request params for AWB: ".$waybillnumber.PHP_EOL.print_r($params,1));
            $this->logger->info("CancelWaybill Response params for AWB: ".$waybillnumber.PHP_EOL.print_r($result,1));
            if($result->CancelWaybillResult->Status->WayBillGenerationStatus->StatusCode == 'CancelFailure') {
                $res_error = $result->CancelWaybillResult->Status->WayBillGenerationStatus->StatusInformation; 
                return array($res_error, 'error');
            } else {
                $res_status = $result->CancelWaybillResult->Status->WayBillGenerationStatus->StatusInformation;
                return array($res_status, 'success');
            }
            exit;
        }catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->info("Exception: ".$message.PHP_EOL);
            return array($message, 'error');
        }
    }

    /***********  Get all Tracking Number assigned to Shipment  **********/
    /*protected function cancelWaybill($order)
    {
        $message = '';$output = array();
        if(!$this->waybillgenerationurl){
            $message = "Please enter Way Bill Generation URL from system configuration.";
            return (array($message, 'error'));
        }
        $trackNumber = array();
        foreach ($order->getTracksCollection() as $track){
            //echo '<pre>';print_r($track->getData());
            if ($track->getCarrierCode() == 'bluedart') {
                $trackNumber[] = $track->getNumber();
            }
        }
        if(count($trackNumber) > 0){
            //print_r($trackNumber);exit();
            foreach ($trackNumber as $key => $waybillnumber) {
                $this->cancelWaybillNumber($waybillnumber);
            }
        }else{
            $message = "There is no Bluedart Tracking number found for the Order ".$order->getIncrementId();
            return (array($message, 'error'));
        }
    }*/
}
