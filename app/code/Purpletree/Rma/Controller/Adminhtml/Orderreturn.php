<?php
/**
 * Purpletree_Rma Orderreturn
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

abstract class Orderreturn extends \Magento\Backend\App\Action
{
    /**
     * Orderreturn Factory
     *
     * @var \Purpletree\Rma\Model\OrderreturnFactory
     */
    protected $_orderreturnFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * constructor
     *
     * @param \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\OrderreturnFactory $orderreturnFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_orderreturnFactory    = $orderreturnFactory;
        $this->_coreRegistry          = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Orderreturn
     *
     * @return \Purpletree\Rma\Model\Orderreturn
     */
    protected function _initOrderreturn()
    {
         $orderreturnId  = (int) $this->getRequest()->getParam('pts_orderreturn_id');
        /** @var \Purpletree\Rma\Model\Orderreturn $orderreturn */
        $orderreturn    = $this->_orderreturnFactory->create();
        if ($orderreturnId) {
            $orderreturn->load($orderreturnId);
        }
        $this->_coreRegistry->register('purpletree_rma_orderreturn', $orderreturn);
        return $orderreturn;
    }
}
