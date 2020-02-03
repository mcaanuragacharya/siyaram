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
namespace Purpletree\Rma\Controller\Adminhtml\Packagecondition;

class Save extends \Purpletree\Rma\Controller\Adminhtml\Packagecondition
{
    protected $_dataHelper;
    
    protected $_context;
    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Purpletree\Rma\Model\PackageconditionFactory $packageconditionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\PackageconditionFactory $packageconditionFactory,
        \Magento\Framework\Registry $registry,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        parent::__construct($packageconditionFactory, $registry, $context);
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
        return $this->_authorization->isAllowed('Purpletree_Rma::packagecondition');
    }
    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('packagecondition');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (!($this->_dataHelper->isEnabled())) {
                $this->messageManager->addError(__('Module is disabled. Please enable the module to Continue'));
                $resultRedirect->setPath('purpletree_rma/*/');
                return $resultRedirect;
            }
            $packagecondition = $this->_initPackagecondition();
            if ($packagecondition->getPtsPackageconditionId() == 1) {
                $this->messageManager->addError(__('Cannot Edit Package Condition: '.$packagecondition->getPtsName()));
            } else {
                $packagecondition->setData($data);
                $this->_eventManager->dispatch(
                    'purpletree_rma_packagecondition_prepare_save',
                    [
                    'packagecondition' => $packagecondition,
                    'request' => $this->getRequest()
                    ]
                );
                try {
                    $packagecondition->save();
                    $this->messageManager->addSuccess(__('The Package Condition has been saved.'));
                    $this->_context->getSession()->setPurpletreeRmaPackageconditionData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $resultRedirect->setPath(
                            'purpletree_rma/*/edit',
                            [
                            'pts_packagecondition_id' => $packagecondition->getPtsPackageconditionId(),
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
                    $this->messageManager->addException($e, __('Something went wrong while saving the Package Condition.'));
                }
                    $this->_getSession()->setPurpletreeRmaPackageconditionData($data);
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
        $resultRedirect->setPath('purpletree_rma/packagecondition/index/');
        return $resultRedirect;
    }
}
