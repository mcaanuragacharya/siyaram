<?php
/**
 * Purpletree_Rma Edit
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
namespace Purpletree\Rma\Block\Adminhtml\Packagecondition;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * constructor
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
    
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Packagecondition edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'pts_packagecondition_id';
        $this->_blockGroup = 'Purpletree_Rma';
        $this->_controller = 'adminhtml_packagecondition';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Package Condition'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Package Condition'));
    }
    /**
     * Retrieve text for header element depending on loaded Packagecondition
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Purpletree\Rma\Model\Packagecondition $packagecondition */
        $packagecondition = $this->_coreRegistry->registry('purpletree_rma_packagecondition');
        if ($packagecondition->getPtsPackageconditionId()) {
            return __("Edit Package Condition '%1'", $this->escapeHtml($packagecondition->getPtsName()));
        }
        return __('New Package Condition');
    }
}
