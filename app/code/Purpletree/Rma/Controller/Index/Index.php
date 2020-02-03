<?php
/**
 * Purpletree_Rma Index
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

class Index extends \Magento\Framework\App\Action\Action
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
    
    protected $_coreRegistry;
    
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
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     * @param \Purpletree\Rma\Helper\Processdata $processdata
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Purpletree\Rma\Helper\Processdata $processdata
    ) {
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_customer             = $customer;
        $this->_storeManager         = $storeManager;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry         = $coreRegistry;
        $this->_dataHelper           = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    public function execute()
    {

        if (!$this->_customer->isLoggedIn()) {
            $this->_customer->setAfterAuthUrl($this->_storeManager->getStore()->getCurrentUrl());
            $this->_customer->authenticate();
        }
        $moduleEnable   =   $this->_dataHelper->isEnabled();
        if (!$moduleEnable) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $this->_coreRegistry->register('Customer_Id', $this->_customer->getCustomerId());
        $this->_resultPage = $this->_resultPageFactory->create();
        $this->_resultPage->getConfig()->getTitle()->set(__('Return Orders'));
        return $this->_resultPage;
    }
}
