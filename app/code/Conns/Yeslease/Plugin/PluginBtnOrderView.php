<?php

namespace Conns\Yeslease\Plugin;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\OrderRepository;

class PluginBtnOrderView
{
protected $object_manager;
protected $_backendUrl;

public function __construct(
ObjectManagerInterface $om,
OrderRepository $orderRepository,
UrlInterface $backendUrl
) {
    $this->object_manager = $om;
    $this->_backendUrl = $backendUrl;
    $this->_orderRepository = $orderRepository;
}

public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $subject )
{
    $order = $this->_orderRepository->get($subject->getOrderId());
    $payment = $order->getPayment();
    $invoice_details = $order->getInvoiceCollection();
    foreach ($invoice_details as $_invoice) {
        echo 'yes';
    }
    if($payment->getMethod()=='progressive'){
        $sendOrder = $this->_backendUrl->getUrl('sales/send/order/order_id/'.$subject->getOrderId() );
        $subject->addButton(
        'sendordersms',
        [
        'label' => __('Verify Yeslease'),
        'onclick' => "setLocation('".$subject->getUrl('yeslease/index/yesajax')."')",
        'class' => 'ship primary'
        ]
        );
    }
    //return null;
}
 
}