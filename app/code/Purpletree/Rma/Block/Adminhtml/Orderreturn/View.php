<?php
/**
 * Purpletree_Rma View
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
 
namespace Purpletree\Rma\Block\Adminhtml\Orderreturn;

class View extends \Magento\Backend\Block\Widget\Form\Container
{
     /**
      * Core registry
      *
      * @var \Magento\Framework\Registry
      */
    protected $_coreRegistry;
    
    protected $_currencyprice;
    
    protected $_productloader;
    
    protected $_order;
    
    protected $_productsFactory;
    
    protected $_messageFactory;
    
    protected $_orderreturn;
    
    protected $_reasonFactory;
    
    protected $_reasonOptions;
    
    protected $_resolutionOptions;
    
    protected $_statusOptions;
    
    protected $_packageconditionOptions;
    
    protected $_statusFactory;
    
    protected $_resolutionFactory;
    
    protected $_packageConditionFactory;

    protected $_dataHelper;
    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Pricing\Helper\Data $currencyprice
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Magento\Sales\Model\Order $order
     * @param \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory
     * @param \Purpletree\Rma\Model\OrderreturnmessagesFactory $messageFactory
     * @param \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn
     * @param \Purpletree\Rma\Model\ResourceModel\Reason $reasonFactory
     * @param \Purpletree\Rma\Model\ResourceModel\Reason\Collection $reasonOptions
     * @param \Purpletree\Rma\Model\ResourceModel\Resolution\Collection $resolutionOptions
     * @param \Purpletree\Rma\Model\ResourceModel\Status\Collection $statusOptions
     * @param \Purpletree\Rma\Model\ResourceModel\Packagecondition\Collection $packageconditionOptions
     * @param \Purpletree\Rma\Model\ResourceModel\Status $statusFactory
     * @param \Purpletree\Rma\Model\ResourceModel\Resolution $resolutionFactory
     * @param \Purpletree\Rma\Model\ResourceModel\Packagecondition $packageConditionFactory
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Pricing\Helper\Data $currencyprice,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Sales\Model\Order $order,
        \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory,
        \Purpletree\Rma\Model\OrderreturnmessagesFactory $messageFactory,
        \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn,
        \Purpletree\Rma\Model\ResourceModel\Reason $reasonFactory,
        \Purpletree\Rma\Model\ResourceModel\Reason\Collection $reasonOptions,
        \Purpletree\Rma\Model\ResourceModel\Resolution\Collection $resolutionOptions,
        \Purpletree\Rma\Model\ResourceModel\Status\Collection $statusOptions,
        \Purpletree\Rma\Model\ResourceModel\Packagecondition\Collection $packageconditionOptions,
        \Purpletree\Rma\Model\ResourceModel\Status $statusFactory,
        \Purpletree\Rma\Model\ResourceModel\Resolution $resolutionFactory,
        \Purpletree\Rma\Model\ResourceModel\Packagecondition $packageConditionFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_coreRegistry             = $coreRegistry;
        $this->_currencyprice            = $currencyprice;
        $this->_productloader            = $productloader;
        $this->_order                    = $order;
        $this->_productsFactory          = $productsFactory;
        $this->_messageFactory           = $messageFactory;
        $this->_orderreturn              = $orderreturn;
        $this->_reasonFactory            = $reasonFactory;
        $this->_reasonOptions            = $reasonOptions;
        $this->_resolutionOptions        = $resolutionOptions;
        $this->_statusOptions            = $statusOptions;
        $this->_packageconditionOptions  = $packageconditionOptions;
        $this->_statusFactory            = $statusFactory;
        $this->_resolutionFactory        = $resolutionFactory;
        $this->_packageConditionFactory  = $packageConditionFactory;
        $this->_dataHelper          = $dataHelper;
        parent::__construct($context, $data);
    }
    
     /**
      * Initialize cms page view block
      *
      * @return void
      */
    public function getOrder()
    {
        $pts_orderreturn_id = $this->getRequest()->getParam('pts_orderreturn_id');
        $orderid = $this->_orderreturn->getOrderIdById($pts_orderreturn_id);
        return $this->_order->loadByIncrementId($orderid);
    }
    protected function _construct()
    {
        $this->_objectId = 'pts_orderreturn_id';
        $this->_blockGroup = 'Purpletree_Rma';
        $this->_controller = 'adminhtml_orderreturn';
        $this->_mode = 'view';
        parent::_construct();
        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');
        $pts_orderreturn_id = $this->getRequest()->getParam('pts_orderreturn_id');
        $statusId = $this->_orderreturn->getStatusIdById($pts_orderreturn_id);
        //$orderid = $this->_orderreturn->getOrderIdById($pts_orderreturn_id);
        $order   = $this->getOrder();
        $this->buttonList->add(
            'orderview',
            [
                    'label' => __('View order'),
                    'class' => 'save',
                    'onclick' => 'window.open(\'' . $this->getUrl(
                        'sales/order/view',
                        [
                                        'order_id' => $order->getId()
                                    ]
                    ) . '\')',
                ],
            -100
        );
        if ($statusId != 2 && $statusId != 3) {
            $this->buttonList->add(
                'returnreceived',
                [
                    'label' => __('Mark Received'),
                    'class' => 'save pts-mark-received pts-mark-2 pts-mark',
                    'onclick' => 'setLocation(\'' . $this->getUrl(
                        '*/*/changestatus',
                        [
                                        'pts_orderreturn_id' => $this->getRequest()->getParam('pts_orderreturn_id'),
                                         'pts_status_id' => 2
                                    ]
                    ) . '\')',
                ],
                -100
            );
        }
        if ($statusId != 3) {
            $this->buttonList->add(
                'returncompleted',
                [
                    'label' => __('Mark Completed'),
                    'class' => 'save pts-mark-completed pts-mark-3 pts-mark',
                    'onclick' => 'setLocation(\'' . $this->getUrl(
                        '*/*/changestatus',
                        [
                                        'pts_orderreturn_id' => $this->getRequest()->getParam('pts_orderreturn_id'),
                                        'pts_status_id' => 3
                                    ]
                    ) . '\')',
                ],
                -100
            );
        }
        if ($statusId == 3) {
            $this->buttonList->add(
                'creditmemo',
                [
                    'label' => __('Create Credit Memo'),
                    'class' => 'save pts-credit-memo',
                    'onclick' => 'window.open(\'' . $this->getUrl(
                        'sales/order_creditmemo/new',
                        [
                                        'order_id' => $order->getId()
                                    ]
                    ) . '\')',
                ],
                -100
            );
        }
    }

    /**
     * Retrieve prepared orderreturnmessage collection
     *
     * @return Purpletree_Rma_Model_Resource_Orderreturnmessage_Collection
     */
    public function getAdminEmail()
    {
        return $this->_dataHelper->getGeneralConfig('/email_configuration/email_id');
    }
    public function getOrderId()
    {
        $pts_orderreturn_id = $this->getRequest()->getParam('pts_orderreturn_id');
        return $this->_orderreturn->getOrderIdById($pts_orderreturn_id);
    }
    public function getCustomerEmail()
    {
        $customerdetails = [];
        $pts_orderreturn_id = $this->getRequest()->getParam('pts_orderreturn_id');
        $orderid = $this->_orderreturn->getOrderIdById($pts_orderreturn_id);
        $order   = $this->_order->loadByIncrementId($orderid);
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
        $pts_orderreturn_id = $this->getRequest()->getParam('pts_orderreturn_id');
        $orderreturnmessage = $this->_messageFactory->create();
        $collection         = $orderreturnmessage->getCollection();
        $collection->addFieldToFilter('pts_orderreturn_id', ['eq' => $pts_orderreturn_id]);
        return $collection;
    }

    public function listStatus()
    {
        
        return $this->_statusOptions->_toOptionArray();
    }
    
    public function listReasons()
    {
        
        return $this->_reasonOptions->_toOptionArray();
    }
    
    public function listresolutions()
    {
        return $this->_resolutionOptions->_toOptionArray();
    }
    
    public function listpackageconditions()
    {
        
        return $this->_packageconditionOptions->_toOptionArray();
    }
    
    public function getOrderreturnStatus($id)
    {
        return $this->_statusFactory->getStatusNameById($id);
    }
    public function getOrderreturnReason()
    {
        $orderreturn = $this->_coreRegistry->registry('purpletree_rma_orderreturn');
        return $this->_reasonFactory->getReasonNameById($orderreturn->getPtsReasonId());
    }
    public function getOrderreturnResolution()
    {
        $orderreturn = $this->_coreRegistry->registry('purpletree_rma_orderreturn');
        return $this->_resolutionFactory->getResolutionNameById($orderreturn->getPtsResolutionId());
    }
    public function getOrderreturnPackageCondition()
    {
        $orderreturn = $this->_coreRegistry->registry('purpletree_rma_orderreturn');
        return $this->_packageConditionFactory->getPackageconditionNameById($orderreturn->getPtsPackageconditionId());
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
        $pts_orderreturn_id = $this->getRequest()->getParam('pts_orderreturn_id');
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
