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
namespace Purpletree\Rma\Controller\Adminhtml;

abstract class Reason extends \Magento\Backend\App\Action
{
    /**
     * Reason Factory
     *
     * @var \Purpletree\Rma\Model\ReasonFactory
     */
    protected $_reasonFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * constructor
     *
     * @param \Purpletree\Rma\Model\ReasonFactory $reasonFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\ReasonFactory $reasonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_reasonFactory           = $reasonFactory;
        $this->_coreRegistry          = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Reason
     *
     * @return \Purpletree\Rma\Model\Reason
     */
    protected function _initReason()
    {
        $reasonId  = (int) $this->getRequest()->getParam('pts_reason_id');
        /** @var \Purpletree\Rma\Model\Reason $reason */
        $reason    = $this->_reasonFactory->create();
        if ($reasonId) {
            $reason->load($reasonId);
        }
        $this->_coreRegistry->register('purpletree_rma_reason', $reason);
        return $reason;
    }
}
