<?php

namespace Conns\Yeslease\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Conns\Yeslease\Helper\Data as HelperData;
use Conns\Restservices\Helper\Restobj;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

class Yesajax extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        HelperData $helperData,
        OrderRepositoryInterface $orderRepository,
        Restobj $restobj,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        InvoiceSender $invoiceSender
    )
    {
        $this->request = $request;
        $this->helperData = $helperData;
        $this->orderRepository = $orderRepository;
        $this->restobj = $restobj;
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceSender;
        return parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $orderId = $this->request->getParam('order_id');
        $result = $this->helperData->getDelivconfirmStatus($orderId);
        if ($result->errorCode == "Y") {
            return;
        }
        $order = $this->orderRepository->get($orderId);
        if (!$order->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
        }
        if ((trim($result->Response->ApprovalStatus) == "Contracts Received" || trim($result->Response->ApprovalStatus) == "Funded")
            && $result->Response->OkayToDeliver == true
        ) {
//            $response = $this->restobj->invoiceNewForSalesOrder($order);
//            print_r($response);
        }
        try {
            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }
            $invoice = $this->invoiceService->prepareInvoice($order);

            if (!$invoice) {
                throw new LocalizedException(__('We can\'t save the invoice right now.'));
            }

            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }
            $invoice->register();

            $invoice->getOrder()->setCustomerNoteNotify(false);
            $invoice->getOrder()->setIsInProcess(true);

            $transactionSave = $this->_objectManager->create(
                \Magento\Framework\DB\Transaction::class
            )->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            $this->messageManager->addSuccess(__('The invoice has been created.'));
            // send invoice/shipment emails
            try {
//            if (!empty($data['send_email'])) {
                $this->invoiceSender->send($invoice);
//            }
            } catch (\Exception $e) {
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->messageManager->addError(__('We can\'t send the invoice email right now.'));
            }
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true);
            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('sales/order_invoice/new', ['order_id' => $orderId]);
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t save the invoice right now.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            return $resultRedirect->setPath('sales/order_invoice/new', ['order_id' => $orderId]);
        }
        /*$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();*/

    }
}