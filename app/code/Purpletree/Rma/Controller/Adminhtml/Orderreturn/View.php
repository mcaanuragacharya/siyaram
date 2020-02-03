<?php
/**
 * Purpletree_Rma Edit
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
 
namespace Purpletree\Rma\Controller\Adminhtml\Orderreturn;

class View extends \Purpletree\Rma\Controller\Adminhtml\Orderreturn
{
    protected $_resultPage;
    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Result JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    protected $_dataHelper;
    
    /**
     * constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $registry,
        \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Purpletree\Rma\Helper\Processdata $processdata
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_dataHelper        = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($orderreturnFactory, $registry, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::orderreturn');
    }
    
    /**
     * execute the action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->getParam('pts_orderreturn_id')) {
            return $this->getResultPage();
        } else {
            return $resultRedirect->setPath('purpletree_rma/*/');
        }
    }
    
    /**
     * instantiate result page object
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function getResultPage()
    {
        if (is_null($this->_resultPage)) {
            $this->_resultPage = $this->_resultPageFactory->create();
            $orderreturn = $this->_initOrderreturn();
            if ($orderreturn->getPtsOrderreturnId()) {
                $this->_resultPage->getConfig()->getTitle()->prepend(__('Return for Order ID #'.$orderreturn->getPtsOrderId()));
                // Changing the page title
                $this->_resultPage->getLayout()->getBlock('orderreturn.message')->setOrderreturn($orderreturn);
            }
        }
        return $this->_resultPage;
    }
}
