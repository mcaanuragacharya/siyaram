<?php
/**
 * Purpletree_Rma Resolution
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

abstract class Resolution extends \Magento\Backend\App\Action
{
    /**
     * Resolution Factory
     *
     * @var \Purpletree\Rma\Model\ResolutionFactory
     */
    protected $_resolutionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * constructor
     *
     * @param \Purpletree\Rma\Model\ResolutionFactory $resolutionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\ResolutionFactory $resolutionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_resolutionFactory   = $resolutionFactory;
        $this->_coreRegistry        = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Resolution
     *
     * @return \Purpletree\Rma\Model\Resolution
     */
    protected function _initResolution()
    {
        $resolutionId  = (int) $this->getRequest()->getParam('pts_resolution_id');
        /** @var \Purpletree\Rma\Model\Resolution $resolution */
        $resolution    = $this->_resolutionFactory->create();
        if ($resolutionId) {
            $resolution->load($resolutionId);
        }
        $this->_coreRegistry->register('purpletree_rma_resolution', $resolution);
        return $resolution;
    }
}
