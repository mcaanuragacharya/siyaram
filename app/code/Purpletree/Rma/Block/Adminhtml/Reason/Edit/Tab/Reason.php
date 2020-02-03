<?php
/**
 * Purpletree_Rma Reason
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
namespace Purpletree\Rma\Block\Adminhtml\Reason\Edit\Tab;

class Reason extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Country options
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_booleanOptions;

    /**
     * constructor
     *
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Yesno $booleanOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
    
        $this->_booleanOptions           = $booleanOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Purpletree\Rma\Model\Reason $reason */
        $reason = $this->_coreRegistry->registry('purpletree_rma_reason');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('reason_');
        $form->setFieldNameSuffix('reason');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Reason Information'),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($reason->getPtsReasonId()) {
            $fieldset->addField(
                'pts_reason_id',
                'hidden',
                ['name' => 'pts_reason_id']
            );
        }
        $fieldset->addField(
            'pts_name',
            'text',
            [
                'name'  => 'pts_name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'class' => 'required-entry', //add multiple classess
            ]
        );
        $fieldset->addField(
            'pts_enabled',
            'select',
            [
                'name'  => 'pts_enabled',
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'values' => $this->_booleanOptions->toOptionArray(),
            ]
        );
        $fieldset->addField(
            'pts_sort_order',
            'text',
            [
                'name'  => 'pts_sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                 'required' => true,
                'class' => 'validate-digits required-entry', //add multiple classess
            ]
        );

        $reasonData = $this->_session->getData('purpletree_rma_reason_data', true);
        if ($reasonData) {
            $reason->addData($reasonData);
        } else {
            if (!$reason->getPtsReasonId()) {
                $reason->addData($reason->getDefaultValues());
            }
        }
        $form->addValues($reason->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Reason');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
