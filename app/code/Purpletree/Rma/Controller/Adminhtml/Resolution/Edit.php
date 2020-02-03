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
namespace Purpletree\Rma\Controller\Adminhtml\Resolution;

class Edit extends \Purpletree\Rma\Controller\Adminhtml\Resolution
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
     * @param \Purpletree\Rma\Model\ResolutionFactory $resolutionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Purpletree\Rma\Model\ResolutionFactory $resolutionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_context = $context;
        parent::__construct($resolutionFactory, $registry, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::resolution');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('pts_resolution_id');
        /** @var \Purpletree\Rma\Model\Resolution $resolution */
        $resolution = $this->_initResolution();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Purpletree_Rma::resolution');
        $resultPage->getConfig()->getTitle()->set(__('Resolutions'));
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            if ($id == 1 || $id == 2 || $id == 3) {
                $this->messageManager->addError(__('Cannot Edit this Resolution.'));
                return $resultRedirect->setPath('purpletree_rma/resolution/index');
            } else {
                $resolution->load($id);
                if (!$resolution->getPtsResolutionId()) {
                    $this->messageManager->addError(__('This Resolution no longer exists.'));
                    $resultRedirect->setPath(
                        'purpletree_rma/*/edit',
                        [
                        'pts_resolution_id' => $resolution->getPtsResolutionId(),
                        '_current' => true
                        ]
                    );
                        return $resultRedirect;
                }
            }
        }
        $title = $resolution->getPtsResolutionId() ? $resolution->getPtsName() : __('New Resolution');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->_context->getSession()->getData('purpletree_rma_resolution_data', true);
        if (!empty($data)) {
            $resolution->setData($data);
        }
        return $resultPage;
    }
}
