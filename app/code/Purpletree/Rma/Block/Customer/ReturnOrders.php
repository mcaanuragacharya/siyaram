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
 
class ReturnOrders extends Template
{
    protected $_order;
   
    protected $_coreRegistry;
   
    protected $_orderreturnFactory;
   
    protected $_statusFactory;
   
    protected $_reasonFactory;
   
    protected $_resolutionFactory;
   
    protected $_packageConditionFactory;
 
   /**
    * @param Template\Context $context
    * @param \Magento\Sales\Model\Order $order
    * @param \Magento\Framework\Registry $coreRegistry
    * @param OrderreturnFactory $orderreturnFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Status $statusFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Reason $reasonFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Resolution $resolutionFactory
    * @param \Purpletree\Rma\Model\ResourceModel\Packagecondition $packageConditionFactory
    */
    public function __construct(
        Template\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Registry $coreRegistry,
        OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Model\ResourceModel\Status $statusFactory,
        \Purpletree\Rma\Model\ResourceModel\Reason $reasonFactory,
        \Purpletree\Rma\Model\ResourceModel\Resolution $resolutionFactory,
        \Purpletree\Rma\Model\ResourceModel\Packagecondition $packageConditionFactory,
        array $data = []
    ) {
        $this->_order = $order;
        $this->_coreRegistry             = $coreRegistry;
        $this->_orderreturnFactory       = $orderreturnFactory;
        $this->_statusFactory            = $statusFactory;
        $this->_reasonFactory            = $reasonFactory;
        $this->_resolutionFactory        = $resolutionFactory;
        $this->_packageConditionFactory  = $packageConditionFactory;
        parent::__construct($context, $data);
    }
 
   /**
    * Set news collection
    */
    protected function _construct()
    {
        parent::_construct();
        $collection = $this->_orderreturnFactory->create()->getCollection()
            ->setOrder('pts_orderreturn_id', 'DESC')
            ->addFieldToFilter('pts_order_id', ['in' => $this->getOrderIds()]);
            ;
        $this->setCollection($collection);
    }
 
   /**
    * @return $this
    */
    public function getOrderIds()
    {
        $customer_id = $this->_coreRegistry->registry('Customer_Id');
         $orders = $this->_order->getCollection()->addAttributeToFilter('customer_id', $customer_id);
         $allOrderIds = [];
        if ($orders) {
            foreach ($orders as $order) {
                $allOrderIds[] = $order->getRealOrderId();
            }
        }
         return $allOrderIds;
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'rma.orders.list.pager'
        );
        $pager->setLimit(10)
            ->setShowAmounts(true)
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
 
        return $this;
    }
 
   /**
    * @return string
    */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
    public function getOrderidfromIncrementId($id)
    {
        return $this->_order->loadByIncrementId($id);
    }
}
