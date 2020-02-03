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
namespace Purpletree\Rma\Controller\Adminhtml;

abstract class Packagecondition extends \Magento\Backend\App\Action
{
    /**
     * Packagecondition Factory
     *
     * @var \Purpletree\Rma\Model\PackageconditionFactory
     */
    protected $_packageconditionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * constructor
     *
     * @param \Purpletree\Rma\Model\PackageconditionFactory $packageconditionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\PackageconditionFactory $packageconditionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_packageconditionFactory           = $packageconditionFactory;
        $this->_coreRegistry          = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Packagecondition
     *
     * @return \Purpletree\Rma\Model\Packagecondition
     */
    protected function _initPackagecondition()
    {
        $packageconditionId  = (int) $this->getRequest()->getParam('pts_packagecondition_id');
        /** @var \Purpletree\Rma\Model\Packagecondition $packagecondition */
        $packagecondition    = $this->_packageconditionFactory->create();
        if ($packageconditionId) {
            $packagecondition->load($packageconditionId);
        }
        $this->_coreRegistry->register('purpletree_rma_packagecondition', $packagecondition);
        return $packagecondition;
    }
}
