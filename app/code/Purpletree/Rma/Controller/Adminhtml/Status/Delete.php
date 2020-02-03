<?php
/**
 * Purpletree_Rma Delete
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

class Delete extends \Purpletree\Rma\Controller\Adminhtml\Status
{
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
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_dataHelper = $objectManager->create('\Purpletree\Rma\Helper\Data');
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('pts_status_id');
        if ($id) {
            if (!($this->_dataHelper->isEnabled())) {
                $this->messageManager->addError(__('Module is disabled. Please enable the module to Continue'));
                $resultRedirect->setPath('purpletree_rma/*/');
                return $resultRedirect;
            }
            if ($id == 1 || $id == 2 || $id == 3) {
                 $this->messageManager->addError(__('Cannot Delete Status ID: '.$id));
            } else {
                $name = "";
                try {
                    /** @var \Purpletree\Rma\Model\Status $status */
                    $status = $this->_statusFactory->create();
                    $status->load($id);
                    $name = $status->getPtsName();
                    $status->delete();
                    $this->messageManager->addSuccess(__('The Status has been deleted.'));
                    $this->_eventManager->dispatch(
                        'adminhtml_purpletree_rma_status_on_delete',
                        ['name' => $name, 'status' => 'success']
                    );
                        $resultRedirect->setPath('purpletree_rma/*/');
                        return $resultRedirect;
                } catch (\Exception $e) {
                    $this->_eventManager->dispatch(
                        'adminhtml_purpletree_rma_status_on_delete',
                        ['name' => $name, 'status' => 'fail']
                    );
                    // display error message
                    $this->messageManager->addError($e->getMessage());
                    // go back to edit form
                    $resultRedirect->setPath('purpletree_rma/*/edit', ['pts_status_id' => $id]);
                    return $resultRedirect;
                }
            }
        } else {
        // display error message
            $this->messageManager->addError(__('Status to delete was not found.'));
        // go to grid
        }
        $resultRedirect->setPath('purpletree_rma/*/');
        return $resultRedirect;
    }
}
