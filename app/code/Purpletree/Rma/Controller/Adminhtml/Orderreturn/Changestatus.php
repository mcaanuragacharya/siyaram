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
 
class Changestatus extends \Purpletree\Rma\Controller\Adminhtml\Orderreturn
{
    protected $_order;
        
    protected $_productsFactory;
        
    protected $_emailHelper;
        
    protected $_dataHelper;
    /**
     * constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Order $order
     * @param \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory
     * @param \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory
     * @param \Purpletree\Rma\Helper\Email $emailHelper
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $order,
        \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory,
        \Purpletree\Rma\Helper\Email $emailHelper,
        \Purpletree\Rma\Helper\Data $dataHelper
    ) {
    
        $this->_order           = $order;
        $this->_productsFactory = $productsFactory;
        $this->_emailHelper         = $emailHelper;
        $this->_dataHelper      = $dataHelper;
        parent::__construct($orderreturnFactory, $registry, $context);
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
        $data = $this->getRequest()->getParams();
        if ($data) {
            $orderreturn = $this->_initOrderreturn();
            try {
                if ($orderreturn->getPtsOrderId()) {
                    $order   = $this->_order->loadByIncrementId($orderreturn->getPtsOrderId());
                    $orderretprocount = count($this->_productsFactory->getProducts($orderreturn->getPtsOrderreturnId()));
                    if ($order) {
                        $created_date = $orderreturn->getPtsCreatedAt();
                        $orderreturn->setPtsStatusId($data['pts_status_id']);
                        $orderreturn->save();
                        //change order status
                        $totalordercount = $order->getTotalItemCount();
                        if ($orderretprocount == $totalordercount && $data['pts_status_id'] == 2) {
                            $status = $this->_dataHelper->getGeneralConfig('/order_status/pts_full_return_initiated');
                            $order->setStatus($status);
                            $order->save();
                        } elseif ($orderretprocount == $totalordercount && $data['pts_status_id'] == 3) {
                            $status = $this->_dataHelper->getGeneralConfig('/order_status/pts_full_return_completed');
                            $order->setStatus($status);
                            $order->save();
                        } elseif ($orderretprocount < $totalordercount && $data['pts_status_id'] == 2) {
                            $status = $this->_dataHelper->getGeneralConfig('/order_status/pts_partial_return_initiated');
                            $order->setStatus($status);
                            $order->save();
                        } elseif ($orderretprocount < $totalordercount && $data['pts_status_id'] == 3) {
                            $status = $this->_dataHelper->getGeneralConfig('/order_status/pts_partial_return_completed');
                            $order->setStatus($status);
                            $order->save();
                        }
                        //change order status
                        if ($data['pts_status_id'] == 3) {
                            $this->messageManager->addSuccess(__('The Order Return has been Completed.'));
                        }
                        if ($data['pts_status_id'] == 2) {
                            $this->messageManager->addSuccess(__('The Order Return has been Received.'));
                        }
                        //Email on status Change to customer
                        try {
                            if ($this->_dataHelper->getGeneralConfig('/email_configuration/emails_to_customer')) {
                                $customer_emails = $this->_dataHelper->getGeneralConfig('/email_configuration/customer_emails');
                                if ($customer_emails != '') {
                                    $all_emails = explode(',', $customer_emails);
                                    if (in_array("customer_status_change_email", $all_emails)) {
                                        $identifier = "status_change_email";
                                        $this->_emailHelper->emailCustomer($data['pts_orderreturn_id'], $data['pts_status_id'], $created_date, $identifier);
                                    }
                                }
                            }
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $this->messageManager->addError($e->getMessage());
                        } catch (\RuntimeException $e) {
                            $this->messageManager->addError($e->getMessage());
                        } catch (\Exception $e) {
                            $this->messageManager->addException($e, __('Something went wrong, Not able to send email to customer.'));
                        }
                        //Email on status Change to customer
                        //SMS on status Change to customer
                        try {
                            if ($this->_dataHelper->getGeneralConfig('/smsapi/sms_enabled')) {
                                if ($this->_dataHelper->getGeneralConfig('/smsapi/sms_status_changed')) {
                                    $sms = $this->_dataHelper->statusTemplate();
                                    $this->_emailHelper->smsToCustomer($data['pts_orderreturn_id'], $data['pts_status_id'], $sms);
                                }
                            }
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $this->messageManager->addError($e->getMessage());
                        } catch (\RuntimeException $e) {
                            $this->messageManager->addError($e->getMessage());
                        } catch (\Exception $e) {
                            $this->messageManager->addException($e, __('Something went wrong while sending SMS to customer.'));
                        }
                        //Email on status Change to customer
                    } else {
                        $this->messageManager->addError(__('Order not found.'));
                    }
                } else {
                    $this->messageManager->addError(__('Order ID not found.'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while Completing the Order Return.'));
            }
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
