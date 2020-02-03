<?php
namespace Bluedart\Shipment\Block\Adminhtml;
use Magento\Backend\Block\Template;

class Pickupregistration extends Template
{
	protected $scopeConfig;

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) { 
    	$this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function getConfig()
    {
        return $this->scopeConfig;
    }
}