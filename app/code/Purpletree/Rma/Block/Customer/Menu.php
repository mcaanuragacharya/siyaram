<?php
/**
 * Purpletree_Rma Menu
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

namespace Purpletree\Rma\Block\Customer;

class Menu extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * Data Helper
     *
     * @var \Purpletree\Rma\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Purpletree\Rma\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Purpletree\Rma\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper       =       $dataHelper;
        parent::__construct($context, $data);
    }
   
    /**
     * Get Current Url
     *
     * @return Current Url
     */
    public function getCurrentUrl()
    {
        return $this->getRequest()->getActionName();
    }
    
    /**
     * Get Module Enabled
     *
     * @return Module Enabled
     */
    public function isModuleEnabled()
    {
        return $this->_dataHelper->isEnabled();
    }
}
