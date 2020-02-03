<?php
/**
 * Purpletree_Rma Initiate
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

namespace Purpletree\Rma\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Initiate extends \Magento\Framework\App\Action\Action
{
     /**
      * Customer Session
      *
      * @var \Magento\Customer\Model\Session
      */
    protected $_customer;
    
    /**
     * Page Factory
     *
     * @var  \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Result Forward Factory
     *
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $_resultForwardFactory;
    
    protected $_order;
    
    protected $_orderreturn;
    /**
     * Data Helper
     *
     * @var \Purpletree\Rma\Helper\Data
     */
    protected $_dataHelper;
    

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Sales\Model\Order $order
     * @param \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     * @param \Purpletree\Rma\Helper\Processdata $processdata
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Sales\Model\Order $order,
        \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Purpletree\Rma\Helper\Processdata $processdata
    ) {
        $this->_customer             = $customer;
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_storeManager         = $storeManager;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_order                = $order;
        $this->_orderreturn          = $orderreturn;
        $this->_dataHelper           = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    public function execute()
    {
        if (!($this->_dataHelper->isEnabled())) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if (!$this->_customer->isLoggedIn()) {
            $this->_customer->setAfterAuthUrl($this->_storeManager->getStore()->getCurrentUrl());
            $this->_customer->authenticate();
        }
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $order   = $this->_order->load($orderId);
            if ($order) {
                 $entityId = $this->_orderreturn->getOrderreturnIdByOrderId($order->getRealOrderId());
                if (!$entityId) {
                    if ($order->getCustomerId()) {
                        if ($order->getCustomerId() == $this->_customer->getCustomerId()) {
                            $this->_resultPage = $this->_resultPageFactory->create();
                            $this->_resultPage->getConfig()->getTitle()->set(__('Return Order : #'.$order->getRealOrderId()));
                            return $this->_resultPage;
                        } else {
                              $this->messageManager->addError(__('This Order Id is not of Logged in customer.'));
                        }
                    } else {
                        $this->messageManager->addError(__('Customer ID in Order not found.'));
                    }
                } else {
                    $this->messageManager->addError(__('Return Order already registered.'));
                }
            } else {
                   $this->messageManager->addError(__('Order not found.'));
            }
        } else {
                $this->messageManager->addError(__('Order ID not found.'));
        }
        return $this->_redirect('sales/order/history');
    }
}
