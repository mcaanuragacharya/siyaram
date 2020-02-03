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

class InitiateGuest extends \Magento\Framework\App\Action\Action
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
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     * @param \Purpletree\Rma\Helper\Processdata $processdata
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customer,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Purpletree\Rma\Helper\Processdata $processdata
    ) {
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_customer             = $customer;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_dataHelper           = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if (!($this->_dataHelper->isEnabled()) && !$data) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if ($this->_customer->isLoggedIn()) {
            return $this->_redirect('sales/order/history');
        }
            $this->_resultPage = $this->_resultPageFactory->create();
            $this->_resultPage->getConfig()->getTitle()->set(__('Return Order : #'.$data['order_id']));
            return $this->_resultPage;
    }
}
