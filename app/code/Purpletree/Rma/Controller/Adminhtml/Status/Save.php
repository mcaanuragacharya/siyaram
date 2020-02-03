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
namespace Purpletree\Rma\Controller\Adminhtml\Status;

class Save extends \Purpletree\Rma\Controller\Adminhtml\Status
{
    protected $_dataHelper;
    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Purpletree\Rma\Model\StatusFactory $statusFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\StatusFactory $statusFactory,
        \Magento\Framework\Registry $registry,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        parent::__construct($statusFactory, $registry, $context);
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
        return $this->_authorization->isAllowed('Purpletree_Rma::status');
    }
    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('status');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (!($this->_dataHelper->isEnabled())) {
                $this->messageManager->addError(__('Module is disabled. Please enable the module to Continue'));
                $resultRedirect->setPath('purpletree_rma/*/');
                return $resultRedirect;
            }
            $status = $this->_initStatus();
            if ($status->getPtsStatusId() == 1 || $status->getPtsStatusId() == 2 || $status->getPtsStatusId() == 3) {
                $this->messageManager->addError(__('Cannot Edit Status: '.$status->getPtsName()));
            } else {
                $status->setData($data);
                $this->_eventManager->dispatch(
                    'purpletree_rma_status_prepare_save',
                    [
                    'status' => $status,
                    'request' => $this->getRequest()
                    ]
                );
                try {
                    $status->save();
                    $this->messageManager->addSuccess(__('The Status has been saved.'));
                    $this->_context->getSession()->setPurpletreeRmaStatusData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $resultRedirect->setPath(
                            'purpletree_rma/*/edit',
                            [
                            'pts_status_id' => $status->getPtsStatusId(),
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
                    $this->messageManager->addException($e, __('Something went wrong while saving the Status.'));
                }
                    $this->_getSession()->setPurpletreeRmaStatusData($data);
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
        $resultRedirect->setPath('purpletree_rma/status/index/');
        return $resultRedirect;
    }
}
