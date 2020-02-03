<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

class Shipmenttransit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        //Call page factory to render layout and page content
        $resultPage = $this->resultPageFactory->create();

        //Set the menu which will be active for this page
        $resultPage->setActiveMenu('Bluedart_Shipment::shipmenttransit');
        
        //Set the header title of grid
        $resultPage->getConfig()->getTitle()->prepend(__('Shipment Transit'));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Bluedart'), __('Bluedart'));
        $resultPage->addBreadcrumb(__('Shipment Transit'), __('Shipment Transit'));

        return $resultPage;
    }

    /*
     * Check permission via ACL resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bluedart_Shipping::bluedart');
    }
}