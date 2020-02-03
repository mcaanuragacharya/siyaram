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
namespace Purpletree\Rma\Controller\Adminhtml\Packagecondition;

class Edit extends \Purpletree\Rma\Controller\Adminhtml\Packagecondition
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
     * @param \Purpletree\Rma\Model\PackageconditionFactory $packageconditionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Purpletree\Rma\Model\PackageconditionFactory $packageconditionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_context = $context;
        parent::__construct($packageconditionFactory, $registry, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::packagecondition');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('pts_packagecondition_id');
        /** @var \Purpletree\Rma\Model\Packagecondition $packagecondition */
        $packagecondition = $this->_initPackagecondition();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Purpletree_Rma::packagecondition');
        $resultPage->getConfig()->getTitle()->set(__('Package Conditions'));
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            if ($id == 1) {
                 $this->messageManager->addError(__('Cannot Edit this Package Condition.'));
                 return $resultRedirect->setPath('purpletree_rma/packagecondition/index');
            } else {
                $packagecondition->load($id);
                if (!$packagecondition->getPtsPackageconditionId()) {
                    $this->messageManager->addError(__('This Package Condition no longer exists.'));
                    $resultRedirect->setPath(
                        'purpletree_rma/*/edit',
                        [
                            'pts_packagecondition_id' => $packagecondition->getPtsPackageconditionId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
            }
        }
        $title = $packagecondition->getPtsPackageconditionId() ? $packagecondition->getPtsName() : __('New Package Condition');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->_context->getSession()->getData('purpletree_rma_packagecondition_data', true);
        if (!empty($data)) {
            $packagecondition->setData($data);
        }
        return $resultPage;
    }
}
