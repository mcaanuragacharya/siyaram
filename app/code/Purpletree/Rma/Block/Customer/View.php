<?php
/**
 * Purpletree_Rma ReturnOrders
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Rma
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Rma\Block\Customer;
 
use Magento\Framework\View\Element\Template;
use Purpletree\Rma\Model\OrderreturnFactory;
 
class View extends Template
{

    protected $_order;
   
    protected $_currencyprice;
   
    protected $_productloader;
   
    protected $_coreRegistry;
   
    protected $_orderreturnFactory;
   
    protected $_orderreturn;
   
    protected $_messageFactory;
   
    protected $_productsFactory;
   
    protected $_statusFactory;
   
    protected $_reasonFactory;
   
    protected $_resolutionFactory;
   
    protected $_packageConditionFactory;
   
    protected $_dataHelper;
   /**
    * @param Template\Context $context
    * @param \Magento\Sales\Model\Order $order
    * @param \Magento\Framework\Pricing\Helper\Data $currencyprice
    * @param \Magento\Catalog\Model\ProductFactory $productloader
    * @param \Magento\Framework\Registry $coreRegistry
    * @param OrderreturnFactory $orderreturnFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn
    * @param \Purpletree\Rma\Model\OrderreturnmessagesFactory $messageFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Status $statusFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Reason $reasonFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Resolution $resolutionFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Packagecondition $packageConditionFactory
    * @param \Purpletree\Rma\Helper\Data $dataHelper
    * @param array $data = []
    */
    public function __construct(
        Template\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Pricing\Helper\Data $currencyprice,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Framework\Registry $coreRegistry,
        OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn,
        \Purpletree\Rma\Model\OrderreturnmessagesFactory $messageFactory,
        \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory,
        \Purpletree\Rma\Model\ResourceModel\Status $statusFactory,
        \Purpletree\Rma\Model\ResourceModel\Reason $reasonFactory,
        \Purpletree\Rma\Model\ResourceModel\Resolution $resolutionFactory,
        \Purpletree\Rma\Model\ResourceModel\Packagecondition $packageConditionFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_order                   = $order;
        $this->_currencyprice           = $currencyprice;
        $this->_productloader           = $productloader;
        $this->_coreRegistry            = $coreRegistry;
        $this->_orderreturnFactory      = $orderreturnFactory;
        $this->_orderreturn             = $orderreturn;
        $this->_messageFactory          = $messageFactory;
        $this->_productsFactory         = $productsFactory;
        $this->_statusFactory           = $statusFactory;
        $this->_reasonFactory           = $reasonFactory;
        $this->_resolutionFactory       = $resolutionFactory;
        $this->_packageConditionFactory = $packageConditionFactory;
        $this->_dataHelper              = $dataHelper;
        parent::__construct($context, $data);
    }
    public function getAdminEmail()
    {
        return $this->_dataHelper->getGeneralConfig('/email_configuration/email_id');
    }
    public function getCustomer()
    {
        return $this->_coreRegistry->registry('customer');
    }
    
    public function getOrderId()
    {
        $pts_orderreturn_id = $this->getRequest()->getParam('id');
        return $this->_orderreturn->getOrderIdById($pts_orderreturn_id);
    }
    public function getCustomerEmail()
    {
        $customerdetails = [];
        $order   = $this->getOrderidfromIncrementId();
        if ($order) {
            if ($order->getCustomerId()) {
                $customerdetails['email'] = $order->getCustomerEmail();
                $customerdetails['name'] = $order->getCustomerFirstname()." ".$order->getCustomerLastname();
            } else {
                $customerdetails['email'] = $order->getBillingAddress()->getEmail();
                $customerdetails['name'] = $order->getBillingAddress()->getFirstname().' '.$order->getBillingAddress()->getLastname();
            }
        }
        return $customerdetails;
    }
    
    public function getMessage()
    {
        $pts_orderreturn_id = $this->getRequest()->getParam('id');
        $orderreturnmessage = $this->_messageFactory->create();
        $collection         = $orderreturnmessage->getCollection();
        $collection->addFieldToFilter('pts_orderreturn_id', ['eq' => $pts_orderreturn_id]);
        return $collection;
    }
    
    public function getOrderreturn()
    {
        return $this->_coreRegistry->registry('purpletree_rma_orderreturn');
    }
    
    public function getOrderreturnReason($id)
    {
        return $this->_reasonFactory->getReasonNameById($id);
    }
    
    public function getOrderreturnResolution($id)
    {
        return $this->_resolutionFactory->getResolutionNameById($id);
    }
    
    public function getOrderreturnPackageCondition($id)
    {
        return $this->_packageConditionFactory->getPackageconditionNameById($id);
    }
    
    public function getOrderreturnStatus($id)
    {
        return $this->_statusFactory->getStatusNameById($id);
    }
    
    public function getOrderidfromIncrementId()
    {
        return $this->_order->loadByIncrementId($this->getOrderId());
    }
    public function getproductfromOrder($id)
    {
        $order = $this->getOrderidfromIncrementId();
        foreach ($order->getAllVisibleItems() as $item) {
            if ($item->getProductId() == $id) {
                return $item;
            }
        }
    }
    
    public function loadproduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
    
    public function getProducts()
    {
        $pts_orderreturn_id = $this->getRequest()->getParam('id');
        return $this->_productsFactory->getProducts($pts_orderreturn_id);
    }
    public function convertToCurrency($price)
    {
        return $this->_currencyprice->currency($price, true, false);
    }
    public function getproductimg($image)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product'.$image;
    }
}
