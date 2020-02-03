<?php
/**
 * Purpletree_Rma SaveMessage
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

namespace Purpletree\Rma\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
 
class SaveMessage extends \Magento\Framework\App\Action\Action
{
    protected $_customer;
    
    protected $_resultForwardFactory;
    
    protected $_order;
    
    protected $_uploadModel;
    
    protected $_fileModel;
    
    protected $_orderreturnFactory;
    
    protected $_orderreturn;
    
    protected $_orderreturnmessagesFactory;
    
    protected $_emailHelper;
    
    protected $_dataHelper;
    

    /**
     * @param Context $context
     * @param Context \Magento\Customer\Model\Session $customer
     * @param Context \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param Context \Magento\Sales\Model\Order $order
     * @param Context \Purpletree\Rma\Model\Upload $uploadModel
     * @param Context \Purpletree\Rma\Model\File $fileModel
     * @param Context \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory
     * @param Context \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn
     * @param Context \Purpletree\Rma\Model\OrderreturnmessagesFactory $orderreturnmessagesFactory
     * @param Context \Purpletree\Rma\Helper\Email $emailHelper
     * @param Context \Purpletree\Rma\Helper\Data $dataHelper
     * @param Context \Purpletree\Rma\Helper\Processdata $processdata
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customer,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Sales\Model\Order $order,
        \Purpletree\Rma\Model\Upload $uploadModel,
        \Purpletree\Rma\Model\File $fileModel,
        \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderreturn,
        \Purpletree\Rma\Model\OrderreturnmessagesFactory $orderreturnmessagesFactory,
        \Purpletree\Rma\Helper\Email $emailHelper,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Purpletree\Rma\Helper\Processdata $processdata
    ) {
        $this->_customer                    = $customer;
        $this->_resultForwardFactory        = $resultForwardFactory;
        $this->_order                       = $order;
        $this->_uploadModel                 = $uploadModel;
        $this->_fileModel                   = $fileModel;
        $this->_orderreturnFactory          = $orderreturnFactory;
        $this->_orderreturn                 = $orderreturn;
        $this->_orderreturnmessagesFactory  = $orderreturnmessagesFactory;
        $this->_emailHelper                 = $emailHelper;
        $this->_dataHelper                  = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!($this->_dataHelper->isEnabled())) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        /* if (!$this->_customer->isLoggedIn()) {
            $this->_customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->_customer->authenticate();
        } */
        $data = $this->getRequest()->getPost();
        if ($data) {
            if ($data['pts_orderreturn_id']) {
                try {
                    $orderreturn    = $this->_orderreturnFactory->create();
                    $orderreturn->load($data['pts_orderreturn_id']);
                    $orderreturnmessage    = $this->_orderreturnmessagesFactory->create();
                    $returnId = $orderreturn->getPtsOrderreturnId();
                    $statusId = $orderreturn->getPtsStatusId();
                    $created_date = $orderreturn->getPtsCreatedAt();
                    $orderreturnmessage->setPtsOrderreturnId($returnId);
                    $orderreturnmessage->setPtsMessageSender(1);
                    $orderreturnmessage->setPtsStatusId($statusId);
                    $orderreturnmessage->setPtsOrderreturnMessage($data['pts_orderreturn_message']);
                    $orderreturnmessage->save();
                    $attachment = $this->_uploadModel->uploadFileAndGetName('pts_orderreturn_message_attachment', $this->_fileModel->getBaseDir(), $data);
                    if (isset($attachment)) {
                            $orderreturnmessage->setPtsOrderreturnMessageAttachment($attachment);
                            $orderreturnmessage->save();
                    }
                    $this->messageManager->addSuccess(__('Message has been saved.'));
                //Email to Admin
                    try {
                        if ($this->_dataHelper->getGeneralConfig('/email_configuration/emails_to_admin')) {
                            $admin_emails = $this->_dataHelper->getGeneralConfig('/email_configuration/admin_emails');
                            if ($admin_emails != '') {
                                $all_emails = explode(',', $admin_emails);
                                if (in_array("admin_comment_email", $all_emails)) {
                                    $message = substr(strip_tags(trim($data['pts_orderreturn_message'])), 0, 10)."...";
                                    $identifier = "admin_message_email";
                                    $this->_emailHelper->emailCustomer($returnId, $statusId, $created_date, $identifier, $message, 1);
                                }
                            }
                        }
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                          $this->messageManager->addError($e->getMessage());
                    } catch (\RuntimeException $e) {
                          $this->messageManager->addError($e->getMessage());
                    } catch (\Exception $e) {
                         $this->messageManager->addError(__('Something went wrong, Not able to send email to customer.'));
                    }
                                
                //Email to Admin
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the Order Return.'));
                }
            }
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
