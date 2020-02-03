<?php
/**
 * Purpletree_Rma Terms
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

namespace Purpletree\Rma\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Terms extends \Magento\Framework\App\Action\Action
{
    
    /**
     * Page Factory
     *
     * @var  \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * Result Forward Factory
     *
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $_resultForwardFactory;
    
    /**
     * Data Helper
     *
     * @var \Purpletree\Rma\Helper\Data
     */
    protected $_dataHelper;


    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Rma\Helper\Processdata $processdata,
        \Purpletree\Rma\Helper\Data $dataHelper
    ) {
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_dataHelper           = $dataHelper;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    public function execute()
    {
        $moduleEnable   =   $this->_dataHelper->isEnabled();
        if (!$moduleEnable) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
          $this->_resultPage = $this->_resultPageFactory->create();
          $this->_resultPage->getConfig()->getTitle()->set(__('Return Terms and Conditions'));
          return $this->_resultPage;
    }
}
