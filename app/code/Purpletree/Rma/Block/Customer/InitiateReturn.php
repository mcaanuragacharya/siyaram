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

use \Magento\Framework\App\ObjectManager;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Sales order history block
 */
class InitiateReturn extends \Magento\Framework\View\Element\Template
{

    protected $_coreRegistry;
    
    protected $_currencyprice;
    
    protected $_order;
    
    protected $_productloader;
    
    protected $_reasonOptions;
    
    protected $_packageconditionOptions;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Pricing\Helper\Data $currencyprice
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Purpletree\Rma\Model\ResourceModel\Reason\Collection $reasonOptions
     * @param \Purpletree\Rma\Model\ResourceModel\Packagecondition\Collection $packageconditionOptions
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Pricing\Helper\Data $currencyprice,
        \Magento\Sales\Model\Order $order,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Purpletree\Rma\Model\ResourceModel\Reason\Collection $reasonOptions,
        \Purpletree\Rma\Model\ResourceModel\Packagecondition\Collection $packageconditionOptions,
        array $data = []
    ) {
        $this->_coreRegistry          = $coreRegistry;
        $this->_currencyprice           = $currencyprice;
        $this->_order                   = $order;
        $this->_productloader           = $productloader;
        $this->_reasonOptions           = $reasonOptions;
        $this->_packageconditionOptions = $packageconditionOptions;
        parent::__construct($context, $data);
    }
        
    public function getEmailId()
    {
         return $this->getRequest()->getParam('email_id');
    }
    public function getOrderId()
    {
             return $this->getRequest()->getParam('order_id');
    }
    public function convertToCurrency($price)
    {
        return $this->_currencyprice->currency($price, true, false);
    }
    public function getproductimg($image)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product'.$image;
    }
    public function loadproduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
    public function getProducts()
    {
             $orderId = $this->getOrderId();
             $order   = $this->_order->load($orderId);
             return $order->getAllVisibleItems();
    }
    public function listPackageConditions()
    {
        return $this->_packageconditionOptions->_toOptionArray();
    }
    public function listReasons()
    {
        return $this->_reasonOptions->_toOptionArray();
    }
}
