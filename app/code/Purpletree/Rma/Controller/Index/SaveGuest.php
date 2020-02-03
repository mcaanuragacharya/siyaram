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

namespace Purpletree\Rma\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class SaveGuest extends \Magento\Framework\App\Action\Action
{
    protected $_customer;
    
    protected $_resultForwardFactory;
    
    protected $_order;
    
    protected $_uploadModel;
    
    protected $_fileModel;
    
    protected $_orderreturnFactory;
    
    protected $_orderreturn;
    
    protected $_orderreturnmessagesFactory;
    
    protected $_orderreturnproductsFactory;
    
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
     * @param Context \Purpletree\Rma\Model\OrderreturnproductsFactory $orderreturnproductsFactory
     * @param Context \Purpletree\Rma\Helper\Email $emailHelper
     * @param Context \Purpletree\Rma\Helper\Data $dataHelper
     * @param Context \Purpletree\Rma\Helper\Processdata $processdata,
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
        \Purpletree\Rma\Model\OrderreturnproductsFactory $orderreturnproductsFactory,
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
        $this->_orderreturnproductsFactory  = $orderreturnproductsFactory;
        $this->_emailHelper                 = $emailHelper;
        $this->_dataHelper                  = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    public function execute()
    {
        if (!($this->_dataHelper->isEnabled())) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if ($this->_customer->isLoggedIn()) {
            return $this->_redirect('sales/order/history');
        }
        $data = $this->getRequest()->getPost();
        if ($data) {
            if (isset($data['order_id'])) {
                $order   = $this->_order->load($data['order_id']);
                if ($order) {
                    if (isset($data['terms_page'])) {
                        $entityId = $this->_orderreturn->getOrderreturnIdByOrderId($order->getRealOrderId());
                        if (!$entityId) {
                            if (isset($data['email_id'])) {
                                if ($order->getBillingAddress()->getEmail() == $data['email_id']) {
                                    try {
                                        $orderreturn    = $this->_orderreturnFactory->create();
                                        $orderreturn->setPtsOrderId($order->getRealOrderId());
                                        $orderreturn->setPtsPackageconditionId($data['package_condition']);
                                        $orderreturn->setPtsReasonId($data['reason']);
                                        $orderreturn->setPtsResolutionId($this->_dataHelper->getGeneralConfig('/general/defaultresolution'));
                                        $orderreturn->setPtsStatusId($this->_dataHelper->getGeneralConfig('/general/defaultstatus'));
                                        $orderreturn->save();
                                        $orderreturnmessage    = $this->_orderreturnmessagesFactory->create();
                                        $returnId = $orderreturn->getPtsOrderreturnId();
                                        $statusId = $orderreturn->getPtsStatusId();
                                        $created_date = $orderreturn->getPtsCreatedAt();
                                        $orderreturnmessage->setPtsOrderreturnId($returnId);
                                        $orderreturnmessage->setPtsMessageSender(1);
                                        $orderreturnmessage->setPtsStatusId($statusId);
                                        $orderreturnmessage->setPtsOrderreturnMessage($data['orderreturn_message']);
                                        $orderreturnmessage->save();
                                        if (isset($data['selected_product']) && !empty($data['selected_product'])) {
                                            foreach ($data['selected_product'] as $selectproducts) {
                                                $orderreturnProducts = $this->_orderreturnproductsFactory->create();
                                                $orderreturnProducts->setPtsOrderreturnId($returnId);
                                                $orderreturnProducts->setPtsProductId($selectproducts);
                                                $orderreturnProducts->setPtsProductQty($data['prodqty'][$selectproducts]);
                                                $orderreturnProducts->save();
                                            }
                                        }
                                        $attachment = $this->_uploadModel->uploadFileAndGetName('pts_orderreturn_message_attachment', $this->_fileModel->getBaseDir(), $data);
                                        if (isset($attachment)) {
                                            $orderreturnmessage->setPtsOrderreturnMessageAttachment($attachment);
                                            $orderreturnmessage->save();
                                        }
                                        $this->messageManager->addSuccess(__('Order Return Initiated Successfully.'));
                        //Email to customer
                                        try {
                                            if ($this->_dataHelper->getGeneralConfig('/email_configuration/emails_to_customer')) {
                                                $customer_emails = $this->_dataHelper->getGeneralConfig('/email_configuration/customer_emails');
                                                if ($customer_emails != '') {
                                                    $all_emails = explode(',', $customer_emails);
                                                    if (in_array("customer_initiate_email", $all_emails)) {
                                                        $identifier = "customer_initiate_email";
                                                        $this->_emailHelper->emailCustomer($returnId, $statusId, $created_date, $identifier);
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
                        //Email to customer
                        //Email to Admin
                                        try {
                                            if ($this->_dataHelper->getGeneralConfig('/email_configuration/emails_to_admin')) {
                                                $admin_emails = $this->_dataHelper->getGeneralConfig('/email_configuration/admin_emails');
                                                if ($admin_emails != '') {
                                                    $all_emails = explode(',', $admin_emails);
                                                    if (in_array("admin_initiate_email", $all_emails)) {
                                                        $identifier = "admin_initiate_email";
                                                        $this->_emailHelper->emailCustomer($returnId, $statusId, $created_date, $identifier, '', 1);
                                                    }
                                                }
                                            }
                                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                              $this->messageManager->addError($e->getMessage());
                                        } catch (\RuntimeException $e) {
                                              $this->messageManager->addError($e->getMessage());
                                        } catch (\Exception $e) {
                                             $this->messageManager->addError(__('Something went wrong, Not able to send email to admin.'));
                                        }
                        //Email to Admin
                        //SMS on Message to customer
                                        try {
                                            if ($this->_dataHelper->getGeneralConfig('/smsapi/sms_enabled')) {
                                                if ($this->_dataHelper->getGeneralConfig('/smsapi/sms_rma_initiate')) {
                                                    $sms = $this->_dataHelper->initiateTemplate();
                                                    $this->_emailHelper->smsToCustomer($returnId, $statusId, $sms);
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
                                        $this->messageManager->addException($e, __('Something went wrong while saving the Order Return.'));
                                    }
                                     return $this->_redirect('rma/index/viewguest', ['id' => $returnId, 'email_id' =>$order->getBillingAddress()->getEmail()]);
                                } else {
                                    $this->messageManager->addError(__('This Order Id is not of Logged in customer.'));
                                }
                            } else {
                                $this->messageManager->addError(__('Customer ID in Order not found.'));
                            }
                        } else {
                            $this->messageManager->addError(__('Return Order already registered.'));
                        }
                    } else {
                        $this->messageManager->addError(__('Please Accept Terms and Conditions to Continue.'));
                    }
                    return $this->_redirect('sales/guest/view', ['order_id' => $order->getId()]);
                } else {
                        $this->messageManager->addError(__('Order not found.'));
                }
            } else {
                $this->messageManager->addError(__('Order ID not found.'));
            }
        }
        return $this->_redirect('sales/guest/form');
    }
}
