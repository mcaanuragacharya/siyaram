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
namespace Purpletree\Rma\Controller\Adminhtml\Resolution;

class Save extends \Purpletree\Rma\Controller\Adminhtml\Resolution
{
    protected $_dataHelper;
    
    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Purpletree\Rma\Model\ResolutionFactory $resolutionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\ResolutionFactory $resolutionFactory,
        \Magento\Framework\Registry $registry,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        parent::__construct($resolutionFactory, $registry, $context);
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
        return $this->_authorization->isAllowed('Purpletree_Rma::resolution');
    }
    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('resolution');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (!($this->_dataHelper->isEnabled())) {
                $this->messageManager->addError(__('Module is disabled. Please enable the module to Continue'));
                $resultRedirect->setPath('purpletree_rma/*/');
                return $resultRedirect;
            }
            $resolution = $this->_initResolution();
            if ($resolution->getPtsResolutionId() == 1 || $resolution->getPtsResolutionId() == 2 || $resolution->getPtsResolutionId() == 3) {
                $this->messageManager->addError(__('Cannot Edit Resolution: '.$resolution->getPtsName()));
            } else {
                $resolution->setData($data);
                $this->_eventManager->dispatch(
                    'purpletree_rma_resolution_prepare_save',
                    [
                    'resolution' => $resolution,
                    'request' => $this->getRequest()
                    ]
                );
                try {
                    $resolution->save();
                    $this->messageManager->addSuccess(__('The Resolution has been saved.'));
                    $this->_context->getSession()->setPurpletreeRmaResolutionData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $resultRedirect->setPath(
                            'purpletree_rma/*/edit',
                            [
                            'pts_resolution_id' => $resolution->getPtsResolutionId(),
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
                    $this->messageManager->addException($e, __('Something went wrong while saving the Resolution.'));
                }
                    $this->_getSession()->setPurpletreeRmaResolutionData($data);
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
        $resultRedirect->setPath('purpletree_rma/resolution/index/');
        return $resultRedirect;
    }
}
