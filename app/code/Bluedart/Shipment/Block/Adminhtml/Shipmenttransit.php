<?php
namespace Bluedart\Shipment\Block\Adminhtml;
use Magento\Backend\Block\Template;

class Shipmenttransit extends Template
{
	public function __construct(
        \Magento\Backend\Block\Template\Context $context
    ) { 
        parent::__construct($context);
    }
}