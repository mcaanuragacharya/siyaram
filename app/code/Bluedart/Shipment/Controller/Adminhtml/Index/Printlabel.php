<?php

namespace Bluedart\Shipment\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Printlabel extends \Magento\Backend\App\Action {

    protected $_scopeConfig;
    protected $_request;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_request = $context->getRequest();
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute() {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $previuosUrl = $this->getUrl('sales/shipment/view', ['shipment_id' => $shipmentId]);
        if ($shipmentId) {
            $shipment = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($shipmentId);
            if ($shipment) {
                try {
                    $trackNumbers = $shipment->getTracksCollection();
                    $trackNumbers->getSelect()->order('entity_id desc')->limit(1);
                    if(count($trackNumbers) > 0){
                        $trackData =  $trackNumbers->getData();//echo '<pre>';print_r($trackData);
                        if ($trackData[0]['carrier_code'] == 'bluedart' ) {
                            $waybillnumber = trim($trackData[0]['track_number']);
                            $filepath = BP."/var/bluedart/".$waybillnumber.".pdf";
                            if (file_exists($filepath)) {
                                $name = "{$waybillnumber}-shipmentlabel.pdf";
                                header('Content-type: application/pdf');
                                header('Content-Disposition: attachment; filename="'.$name.'"');
                                readfile($filepath);
                                exit();
                            }else{
                                $this->messageManager->addError('Shipment label does not exists for this shipment.');    
                            }
                        }
                        else{
                            $this->messageManager->addError('There is no Bluedart Shipment label found for this shipment.');
                        }
                    }else{
                        $this->messageManager->addError('There is no Shipment label found for this shipment.');
                    }
                    $resultRedirect->setUrl($previuosUrl);
                    return $resultRedirect;
                }catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setUrl($previuosUrl);
                    return $resultRedirect;
                }
            }else{
                $this->messageManager->addError('Shipment is empty or not created yet.');
                $resultRedirect->setUrl($previuosUrl);
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addError('Shipment does not exists.');
            $resultRedirect->setUrl($this->getUrl('sales/shipment/index/'));
            return $resultRedirect;
        }
    }
}
