<?php
namespace Conns\Yeslease\Model\Payment;

class Progressive extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    const PAYMENT_METHOD_PROGRESSIVE_CODE = 'progressive';
	
	protected $_code = self::PAYMENT_METHOD_PROGRESSIVE_CODE;
}