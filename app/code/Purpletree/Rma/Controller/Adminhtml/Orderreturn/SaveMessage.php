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
namespace Purpletree\Rma\Controller\Adminhtml\Orderreturn;

use Magento\Framework\Controller\ResultFactory;
 
class SaveMessage extends \Magento\Backend\App\Action
{
    protected $_jsonFactory;
    
    protected $_orderreturnmessagesFactory;
    
    protected $_orderreturnFactory;
    
    protected $_uploadModel;
    
    protected $_fileModel;
    
    protected $_emailHelper;
    
    protected $_dataHelper;
    /**
     * constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
     * @param \Purpletree\Rma\Model\OrderreturnmessagesFactory $orderreturnmessagesFactory,
     * @param \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
     * @param \Purpletree\Rma\Model\Upload $uploadModel,
     * @param \Purpletree\Rma\Model\File $fileModel,
     * @param \Purpletree\Rma\Helper\Email $emailHelper,
     * @param \Purpletree\Rma\Helper\Data $dataHelper,
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Purpletree\Rma\Model\OrderreturnmessagesFactory $orderreturnmessagesFactory,
        \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Model\Upload $uploadModel,
        \Purpletree\Rma\Model\File $fileModel,
        \Purpletree\Rma\Helper\Email $emailHelper,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Purpletree\Rma\Helper\Processdata $processdata
    ) {
    
        $this->_jsonFactory                 = $jsonFactory;
        $this->_orderreturnmessagesFactory  = $orderreturnmessagesFactory;
        $this->_orderreturnFactory          = $orderreturnFactory;
        $this->_uploadModel                 = $uploadModel;
        $this->_fileModel                   = $fileModel;
        $this->_emailHelper                 = $emailHelper;
        $this->_dataHelper                  = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Rma::orderreturn');
    }
    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
         $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!($this->_dataHelper->isEnabled())) {
            $this->messageManager->addError(__('Module is disabled. Please enable the module to Continue.'));
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
        $data = $this->getRequest()->getPost();
        if ($data) {
            $orderreturnmessage    = $this->_orderreturnmessagesFactory->create();
            $orderreturn    = $this->_orderreturnFactory->create();
            if ($data['pts_orderreturn_id']) {
                $orderreturn->load($data['pts_orderreturn_id']);
                $status = $orderreturn->getPtsStatusId();
                $orderreturnmessage->setPtsStatusId($status);
                $created_date = $orderreturn->getPtsCreatedAt();
            }
            $orderreturnmessage->setPtsOrderreturnId($data['pts_orderreturn_id']);
            $orderreturnmessage->setPtsOrderreturnMessage(trim($data['pts_orderreturn_message']));
            $orderreturnmessage->setPtsMessageSender(0);
            try {
                $orderreturnmessage->save();
                $this->messageManager->addSuccess(__('Message has been Saved Successfully for the request.'));
                $attachment = $this->_uploadModel->uploadFileAndGetName('pts_orderreturn_message_attachment', $this->_fileModel->getBaseDir(), $data);
                if (isset($attachment)) {
                        $orderreturnmessage->setPtsOrderreturnMessageAttachment($attachment);
                        $orderreturnmessage->save();
                }
                //Email on Message to customer
                try {
                    if ($this->_dataHelper->getGeneralConfig('/email_configuration/emails_to_customer')) {
                        $customer_emails = $this->_dataHelper->getGeneralConfig('/email_configuration/customer_emails');
                        if ($customer_emails != '') {
                            $all_emails = explode(',', $customer_emails);
                            if (in_array("customer_status_change_email", $all_emails)) {
                                $message = substr(strip_tags(trim($data['pts_orderreturn_message'])), 0, 10)."...";
                                $identifier = "customer_message_email";
                                $this->_emailHelper->emailCustomer($data['pts_orderreturn_id'], $status, $created_date, $identifier, $message, 0);
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
                //Email on Message to customer
                //SMS on Message to customer
                try {
                    if ($this->_dataHelper->getGeneralConfig('/smsapi/sms_enabled')) {
                        if ($this->_dataHelper->getGeneralConfig('/smsapi/sms_rma_comment')) {
                            $sms = $this->_dataHelper->messageTemplate();
                            $this->_emailHelper->smsToCustomer($data['pts_orderreturn_id'], $status, $sms);
                        }
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                      $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                     $this->messageManager->addError(__('Something went wrong while sending SMS to customer.'));
                }
                //SMS on Message to customer
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                  $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                  $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                 $this->messageManager->addError(__('Something went wrong while sending message.'));
            }
        }
          return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
