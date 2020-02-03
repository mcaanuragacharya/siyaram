<?php
/**
 * Purpletree_Rma Status
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

abstract class Status extends \Magento\Backend\App\Action
{
    /**
     * Status Factory
     *
     * @var \Purpletree\Rma\Model\StatusFactory
     */
    protected $_statusFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * constructor
     *
     * @param \Purpletree\Rma\Model\StatusFactory $statusFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Rma\Model\StatusFactory $statusFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_statusFactory   = $statusFactory;
        $this->_coreRegistry    = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Status
     *
     * @return \Purpletree\Rma\Model\Status
     */
    protected function _initStatus()
    {
        $statusId  = (int) $this->getRequest()->getParam('pts_status_id');
        /** @var \Purpletree\Rma\Model\Status $status */
        $status    = $this->_statusFactory->create();
        if ($statusId) {
            $status->load($statusId);
        }
        $this->_coreRegistry->register('purpletree_rma_status', $status);
        return $status;
    }
}
