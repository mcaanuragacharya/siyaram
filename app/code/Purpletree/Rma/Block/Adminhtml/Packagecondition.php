<?php
/**
 * Purpletree_Rma Packagecondition
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
namespace Purpletree\Rma\Block\Adminhtml;

class Packagecondition extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_packagecondition';
        $this->_blockGroup = 'Purpletree_Rma';
        $this->_headerText = __('Package Conditions');
        $this->_addButtonLabel = __('Create New Package Condition');
        parent::_construct();
    }
}