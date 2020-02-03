<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace BDPay\Billdesk\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use BDPay\Billdesk\Model\Billdesk;

class BilldeskConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCode = Billdesk::PAYMENT_METHOD_BILLDESK_CODE;

    protected $method;

    protected $escaper;

    
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        Billdesk $billdesk
    ) {
        $this->escaper = $escaper;
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
        $this->billdesk = $billdesk;
    }

    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                'billdesk' => [
                    "title" => $this->billdesk->getTitle(),
                   
                ],
            ],
        ] : [];
    }
}
