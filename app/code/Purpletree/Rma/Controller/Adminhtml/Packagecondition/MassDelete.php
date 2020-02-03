<?php
/**
 * Purpletree_Rma MassDelete
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

class MassDelete extends \Magento\Backend\App\Action
{
    protected $_dataHelper;

    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * Collection Factory
     *
     * @var \Purpletree\Rma\Model\ResourceModel\Packagecondition\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * constructor
     *
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Purpletree\Rma\Model\ResourceModel\Packagecondition\CollectionFactory $collectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Purpletree\Rma\Model\ResourceModel\Packagecondition\CollectionFactory $collectionFactory,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_filter            = $filter;
        $this->_dataHelper = $dataHelper;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
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
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        if (!($this->_dataHelper->isEnabled())) {
                $this->messageManager->addError(__('Module is disabled. Please enable the module to Continue'));
                $resultRedirect->setPath('purpletree_rma/*/');
                return $resultRedirect;
        }
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $delete = 0;
        foreach ($collection as $item) {
            if ($item->getPtsPackageconditionId() == 1) {
                $this->messageManager->addError(__('Cannot Delete Package Condition: '.$item->getPtsName()));
            } else {
            /** @var \Purpletree\Rma\Model\Packagecondition $item */
                $item->delete();
                $delete++;
            }
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $delete));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        
        return $resultRedirect->setPath('*/*/');
    }
}
