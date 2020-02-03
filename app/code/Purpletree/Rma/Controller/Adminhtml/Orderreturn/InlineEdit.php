<?php
/**
 * Purpletree_Rma InlineEdit
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

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    protected $_jsonFactory;
    
    protected $_order;
    
    protected $_orderreturnFactory;
    
    protected $_productsFactory;
    
    protected $_emailHelper;
    
    protected $_dataHelper;

    /**
     * constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Sales\Model\Order $order
     * @param \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory
     * @param \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory
     * @param \Purpletree\Rma\Helper\Email $emailHelper
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Sales\Model\Order $order,
        \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
        \Purpletree\Rma\Model\ResourceModel\Orderreturnproducts $productsFactory,
        \Purpletree\Rma\Helper\Email $emailHelper,
        \Purpletree\Rma\Helper\Data $dataHelper
    ) {
    
        $this->_jsonFactory         = $jsonFactory;
        $this->_order               = $order;
        $this->_orderreturnFactory  = $orderreturnFactory;
        $this->_productsFactory     = $productsFactory;
        $this->_emailHelper         = $emailHelper;
        $this->_dataHelper          = $dataHelper;
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
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
         //$processdata->getProcessingdata();
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_jsonFactory->create();
        $error = false;
        $messages = [];
        $orderreturnItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($orderreturnItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        if (!($this->_dataHelper->isEnabled())) {
             return $resultJson->setData([
                'messages' => [__('Module is disabled. Please enable the module to Continue.')],
                'error' => true,
             ]);
        }
        foreach (array_keys($orderreturnItems) as $orderreturnId) {
            /** @var \Purpletree\Rma\Model\Orderreturn $orderreturn */
            $orderreturn = $this->_orderreturnFactory->create()->load($orderreturnId);
            try {
                $orderreturnData = $orderreturnItems[$orderreturnId];//todo: handle dates
                $orderincrid =  $orderreturn->getPtsOrderId();
                $old_status_id = $orderreturn->getPtsStatusId();
                $created_date = $orderreturn->getPtsCreatedAt();
                 $orderretprocount = count($this->_productsFactory->getProducts($orderreturn->getPtsOrderreturnId()));
                // $this->_status->getStatusNameById($old_status_id);
                $new_status_id = $orderreturnData['pts_status_id'];
                // $this->_status->getStatusNameById($new_status_id);
                if ($old_status_id != $new_status_id) {
                    $orderreturn->addData($orderreturnData);
                    $orderreturn->save();
                    $order   = $this->_order->loadByIncrementId($orderincrid);
                //change order status
                    $totalordercount = $order->getTotalItemCount();
                    if ($orderretprocount == $totalordercount && $new_status_id == 2) {
                        $status = $this->_dataHelper->getGeneralConfig('/order_status/pts_full_return_initiated');
                        $order->setStatus($status);
                        $order->save();
                    } elseif ($orderretprocount == $totalordercount && $new_status_id == 3) {
                        $status = $this->_dataHelper->getGeneralConfig('/order_status/pts_full_return_completed');
                        $order->setStatus($status);
                        $order->save();
                    } elseif ($orderretprocount < $totalordercount && $new_status_id == 2) {
                        $status = $this->_dataHelper->getGeneralConfig('/order_status/pts_partial_return_initiated');
                        $order->setStatus($status);
                        $order->save();
                    } elseif ($orderretprocount < $totalordercount && $new_status_id == 3) {
                        $status = $this->_dataHelper->getGeneralConfig('/order_status/pts_partial_return_completed');
                        $order->setStatus($status);
                        $order->save();
                    }
                //change order status
                    //Email on Status Change
                    try {
                        if ($this->_dataHelper->getGeneralConfig('/email_configuration/emails_to_customer')) {
                            $customer_emails = $this->_dataHelper->getGeneralConfig('/email_configuration/customer_emails');
                            if ($customer_emails != '') {
                                $all_emails = explode(',', $customer_emails);
                                if (in_array("customer_comment_email", $all_emails)) {
                                        $identifier = "status_change_email";
                                        $this->_emailHelper->emailCustomer($orderreturnId, $new_status_id, $created_date, $identifier);
                                }
                            }
                        }
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $messages[] = $this->getErrorWithOrderreturnId($orderreturn, $e->getMessage());
                        $error = true;
                    } catch (\RuntimeException $e) {
                        $messages[] = $this->getErrorWithOrderreturnId($orderreturn, $e->getMessage());
                        $error = true;
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithOrderreturnId(
                            $orderreturn,
                            __('Something went wrong, Not able to send email to customer.')
                        );
                        $error = true;
                    }
                    //Email on Status Change
                    //SMS on Status Change
                    try {
                        if ($this->_dataHelper->getGeneralConfig('/email_configuration/emails_to_customer')) {
                            if ($this->_dataHelper->getGeneralConfig('/email_configuration/customer_status_change_email')) {
                                $sms = $this->_dataHelper->statusTemplate();
                                $this->_emailHelper->smsToCustomer($orderreturnId, $new_status_id, $sms);
                            }
                        }
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $messages[] = $this->getErrorWithOrderreturnId($orderreturn, $e->getMessage());
                        $error = true;
                    } catch (\RuntimeException $e) {
                        $messages[] = $this->getErrorWithOrderreturnId($orderreturn, $e->getMessage());
                        $error = true;
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithOrderreturnId(
                            $orderreturn,
                            __('Something went wrong while sending SMS to customer.')
                        );
                        $error = true;
                    }
                    //SMS on Status Change
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithOrderreturnId($orderreturn, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithOrderreturnId($orderreturn, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithOrderreturnId(
                    $orderreturn,
                    __('Something went wrong while saving the Order Return.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Orderreturn id to error message
     *
     * @param \Purpletree\Rma\Model\Orderreturn $orderreturn
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithOrderreturnId(\Purpletree\Rma\Model\Orderreturn $orderreturn, $errorText)
    {
        return '[Order Return ID: ' . $orderreturn->getPtsOrderreturnId() . '] ' . $errorText;
    }
}
