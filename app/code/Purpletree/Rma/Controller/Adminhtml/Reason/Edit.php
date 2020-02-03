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
namespace Purpletree\Rma\Controller\Adminhtml\Reason;

class Edit extends \Purpletree\Rma\Controller\Adminhtml\Reason
{
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

    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Purpletree\Rma\Model\ReasonFactory $reasonFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Purpletree\Rma\Model\ReasonFactory $reasonFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_context = $context;
        parent::__construct($reasonFactory, $registry, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::reason');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('pts_reason_id');
        /** @var \Purpletree\Rma\Model\Reason $reason */
        $reason = $this->_initReason();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Purpletree_Rma::reason');
        $resultPage->getConfig()->getTitle()->set(__('Reasons'));
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            if ($id == 1) {
                 $this->messageManager->addError(__('Cannot Edit this Reason.'));
                 return $resultRedirect->setPath('purpletree_rma/reason/index');
            } else {
                $reason->load($id);
                if (!$reason->getPtsReasonId()) {
                    $this->messageManager->addError(__('This Reason no longer exists.'));
                    $resultRedirect->setPath(
                        'purpletree_rma/*/edit',
                        [
                        'pts_reason_id' => $reason->getPtsReasonId(),
                        '_current' => true
                        ]
                    );
                        return $resultRedirect;
                }
            }
        }
        $title = $reason->getPtsReasonId() ? $reason->getPtsName() : __('New Reason');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->_context->getSession()->getData('purpletree_rma_reason_data', true);
        if (!empty($data)) {
            $reason->setData($data);
        }
        return $resultPage;
    }
}
