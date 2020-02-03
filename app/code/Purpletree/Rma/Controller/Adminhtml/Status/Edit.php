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
namespace Purpletree\Rma\Controller\Adminhtml\Status;

class Edit extends \Purpletree\Rma\Controller\Adminhtml\Status
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
     * @param \Purpletree\Rma\Model\StatusFactory $statusFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Purpletree\Rma\Model\StatusFactory $statusFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_context = $context;
        parent::__construct($statusFactory, $registry, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::status');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('pts_status_id');
        /** @var \Purpletree\Rma\Model\Status $status */
        $status = $this->_initStatus();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Purpletree_Rma::status');
        $resultPage->getConfig()->getTitle()->set(__('Status'));
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            if ($id == 1 || $id == 2 || $id == 3) {
                 $this->messageManager->addError(__('Cannot Edit this Status.'));
                 return $resultRedirect->setPath('purpletree_rma/status/index');
            } else {
                $status->load($id);
                if (!$status->getPtsStatusId()) {
                    $this->messageManager->addError(__('This Status no longer exists.'));
                    $resultRedirect->setPath(
                        'purpletree_rma/*/edit',
                        [
                        'pts_status_id' => $status->getPtsStatusId(),
                        '_current' => true
                        ]
                    );
                        return $resultRedirect;
                }
            }
        }
        $title = $status->getPtsStatusId() ? $status->getPtsName() : __('New Status');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->_context->getSession()->getData('purpletree_rma_status_data', true);
        if (!empty($data)) {
            $status->setData($data);
        }
        return $resultPage;
    }
}
