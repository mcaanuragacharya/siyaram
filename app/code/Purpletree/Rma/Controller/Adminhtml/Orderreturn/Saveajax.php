<?php
/**
 * Purpletree_Rma Saveajax
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

class Saveajax extends \Purpletree\Rma\Controller\Adminhtml\Orderreturn
{

    protected $_order;
    
    protected $_jsonFactory;
    
    protected $_resolution;
    
    protected $_status;
    
    protected $_productsFactory;
    
    protected $_emailHelper;
    
    protected $_dataHelper;

    /**
     * constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Sales\Model\Order $order,
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
     * @param \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
     * @param \Purpletree\Rma\Model\ResourceModel\Resolution $resolution,
     * @param \Purpletree\Rma\Model\ResourceModel\Status $status,
     * @param \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory,
     * @param \Purpletree\Rma\Helper\Email $emailHelper,
     * @param \Purpletree\Rma\Helper\Data $dataHelper,
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Model\ResourceModel\Resolution $resolution,
        \Purpletree\Rma\Model\ResourceModel\Status $status,
        \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory,
        \Purpletree\Rma\Helper\Email $emailHelper,
        \Purpletree\Rma\Helper\Data $dataHelper
    ) {
    
        $this->_order           = $order;
        $this->_jsonFactory     = $jsonFactory;
        $this->_resolution      = $resolution;
        $this->_status          = $status;
        $this->_productsFactory = $productsFactory;
        $this->_emailHelper     = $emailHelper;
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
        $status        = 0;
        $new_status_id = 0;
        $resolution    = 0;
        $statuslabel   = 0;
        $resultJson    = $this->_jsonFactory->create();
        $error         = false;
        $success       = false;
        $messages      = [];
        $messageserror = [];
        $data = $this->getRequest()->getParams();
        if (!($this->getRequest()->getParam('isAjax'))) {
            return $resultJson->setData([
              'messageserror' => [__('Please correct the data sent.')],
              'error' => true,
            ]);
        }
        if (!($this->_dataHelper->isEnabled())) {
             return $resultJson->setData([
                'messageserror' => [__('Module is disabled. Please enable the module to Continue.')],
                'error' => true,
             ]);
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $orderreturn = $this->_initOrderreturn();
            $orderincrid =  $orderreturn->getPtsOrderId();
            $old_status_id = $orderreturn->getPtsStatusId();
            $created_date = $orderreturn->getPtsCreatedAt();
            $orderretprocount = count($this->_productsFactory->getProducts($orderreturn->getPtsOrderreturnId()));
            $new_status_id = $data['pts_status_id'];
            $orderreturn->setData($data);
            try {
                $orderreturn->save();
                
                $order   = $this->_order->loadByIncrementId($orderincrid);
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
                $statuslabel = $order->getStatusLabel();
                $success        = true;
                $messages[] = __('The Order Return has been Updated Successfully.');
                $status     = $this->_status->getStatusNameById($orderreturn->getPtsStatusId());
                $resolution = $this->_resolution->getResolutionNameById($orderreturn->getPtsResolutionId());
                if ($old_status_id != $new_status_id) {
                    //Email on status Change to customer
                    try {
                        if ($this->_dataHelper->getGeneralConfig('/email_configuration/emails_to_customer')) {
                            $customer_emails = $this->_dataHelper->getGeneralConfig('/email_configuration/customer_emails');
                            if ($customer_emails != '') {
                                $all_emails = explode(',', $customer_emails);
                                if (in_array("customer_status_change_email", $all_emails)) {
                                    $identifier = "status_change_email";
                                    $this->_emailHelper->emailCustomer($orderreturn->getPtsOrderreturnId(), $orderreturn->getPtsStatusId(), $created_date, $identifier);
                                }
                            }
                        }
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $messageserror[] = $e->getMessage();
                        $error = true;
                    } catch (\RuntimeException $e) {
                        $messageserror[] = $e->getMessage();
                        $error = true;
                    } catch (\Exception $e) {
                        $messageserror[] =  __('Something went wrong,Not able to send email to customer.');
                        $error = true;
                    }
                    //Email on status Change to customer
                    //SMS on status Change to customer
                    try {
                        if ($this->_dataHelper->getGeneralConfig('/smsapi/sms_enabled')) {
                            if ($this->_dataHelper->getGeneralConfig('/smsapi/sms_status_changed')) {
                                    $sms = $this->_dataHelper->statusTemplate();
                                    $this->_emailHelper->smsToCustomer($orderreturn->getPtsOrderreturnId(), $orderreturn->getPtsStatusId(), $sms);
                            }
                        }
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $messageserror[] = $e->getMessage();
                        $error = true;
                    } catch (\RuntimeException $e) {
                        $messageserror[] = $e->getMessage();
                        $error = true;
                    } catch (\Exception $e) {
                        $messageserror[] =  __('Something went wrong while sending SMS to customer.');
                        $error = true;
                    }
                    //SMS on status Change to customer
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messageserror[] = $e->getMessage();
                $error = true;
            } catch (\RuntimeException $e) {
                $messageserror[] = $e->getMessage();
                $error = true;
            } catch (\Exception $e) {
                $messageserror[] =  __('Something went wrong while saving the Order Return.');
                $error = true;
            }
        }
         return $resultJson->setData([
            'messages' => $messages,
            'messageserror' => $messageserror,
            'error' => $error,
            'success' => $success,
            'status' => $status,
            'statusid' => $new_status_id,
            'resolution' => $resolution,
            'statuslabel' => $statuslabel
         ]);
    }
}
