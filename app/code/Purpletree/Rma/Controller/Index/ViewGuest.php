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

namespace Purpletree\Rma\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class ViewGuest extends \Magento\Framework\App\Action\Action
{
    
    /**
     * Page Factory
     *
     * @var  \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customer;
    
    /**
     * Result Forward Factory
     *
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $_resultForwardFactory;
    
    protected $_coreRegistry;
    
    protected $_order;
    
    protected $_orderreturn;
    
    protected $_orderreturnFactory;
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
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Model\Order $order
     * @param \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn
     * @param \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     * @param \Purpletree\Rma\Helper\Processdata $processdata
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customer,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Order $order,
        \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn,
        \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Purpletree\Rma\Helper\Processdata $processdata
    ) {
        $this->_resultPageFactory   = $resultPageFactory;
        $this->_customer            = $customer;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry        = $coreRegistry;
        $this->_order               = $order;
        $this->_orderreturn         = $orderreturn;
        $this->_orderreturnFactory  = $orderreturnFactory;
        $this->_dataHelper          = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->_customer->isLoggedIn()) {
              return $this->_redirect('sales/order/history');
        }
        $this->_coreRegistry->register('customer', 'notloggedin');
        $data = $this->getRequest()->getParams();
        $moduleEnable   =   $this->_dataHelper->isEnabled();
        if (!$moduleEnable) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $data = $this->getRequest()->getParams();
        if ($data) {
            if (isset($data['id'])) {
                $orderincrid = $this->_orderreturn->getOrderIdById($data['id']);
                $order   = $this->_order->loadByIncrementId($orderincrid);
                if ($order) {
                    if (isset($data['email_id'])) {
                        if ($order->getBillingAddress()->getEmail() == $data['email_id']) {
                            $this->_initOrderreturn();
                            $this->_resultPage = $this->_resultPageFactory->create();
                            $this->_resultPage->getConfig()->getTitle()->set(__('Return Order'));
                            return $this->_resultPage;
                        }
                    }
                }
            }
        }
          return $this->_redirect('sales/guest/form');
    }
        /**
         * Init Orderreturn
         *
         * @return \Purpletree\Rma\Model\Orderreturn
         */
    protected function _initOrderreturn()
    {
         $orderreturnId  = (int) $this->getRequest()->getParam('id');
        /** @var \Purpletree\Rma\Model\Orderreturn $orderreturn */
        $orderreturn    = $this->_orderreturnFactory->create();
        if ($orderreturnId) {
            $orderreturn->load($orderreturnId);
        }
        $this->_coreRegistry->register('purpletree_rma_orderreturn', $orderreturn);
        return $orderreturn;
    }
}
