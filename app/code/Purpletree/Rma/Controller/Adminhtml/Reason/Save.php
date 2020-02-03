<?php
/**
 * Purpletree_Rma Save
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

class Save extends \Purpletree\Rma\Controller\Adminhtml\Reason
{
    protected $_dataHelper;
    
    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Purpletree\Rma\Model\ReasonFactory $reasonFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\ReasonFactory $reasonFactory,
        \Magento\Framework\Registry $registry,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        parent::__construct($reasonFactory, $registry, $context);
        $this->_context = $context;
        $this->_dataHelper = $dataHelper;
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
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('reason');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (!($this->_dataHelper->isEnabled())) {
                $this->messageManager->addError(__('Module is disabled. Please enable the module to Continue'));
                $resultRedirect->setPath('purpletree_rma/*/');
                return $resultRedirect;
            }
            $reason = $this->_initReason();
            if ($reason->getPtsReasonId() == 1) {
                $this->messageManager->addError(__('Cannot Edit Reason: '.$reason->getPtsName()));
            } else {
                $reason->setData($data);
                $this->_eventManager->dispatch(
                    'purpletree_rma_reason_prepare_save',
                    [
                    'reason' => $reason,
                    'request' => $this->getRequest()
                    ]
                );
                try {
                    $reason->save();
                    $this->messageManager->addSuccess(__('The Reason has been saved.'));
                    $this->_context->getSession()->setPurpletreeRmaReasonData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $resultRedirect->setPath(
                            'purpletree_rma/*/edit',
                            [
                            'pts_reason_id' => $reason->getPtsReasonId(),
                            '_current' => true
                            ]
                        );
                        return $resultRedirect;
                    }
                    $resultRedirect->setPath('purpletree_rma/*/');
                    return $resultRedirect;
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the Reason.'));
                }
                $this->_getSession()->setPurpletreeRmaReasonData($data);
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
        $resultRedirect->setPath('purpletree_rma/reason/index/');
        return $resultRedirect;
    }
}
