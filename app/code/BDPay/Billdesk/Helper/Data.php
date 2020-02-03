<?php

namespace BDPay\Billdesk\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper{
   
   /**
     * Path to store config if extension is enabled
     *
     * @var string
     */
    const BILLDESK_ACTIVE = 'bdpay/billdesk/active';

    /**
     * Check if extension enabled
     *
     * @return string|null
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::BILLDESK_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}