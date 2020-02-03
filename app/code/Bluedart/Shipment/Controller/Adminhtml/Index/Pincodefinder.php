<?php
namespace Bluedart\Shipment\Controller\Adminhtml\Index;

class Pincodefinder extends \Magento\Backend\App\Action
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
        $resultPage->setActiveMenu('Bluedart_Shipment::pincodefinder');
        
        //Set the header title of grid
        $resultPage->getConfig()->getTitle()->prepend(__('Pincode Finder'));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Bluedart'), __('Bluedart'));
        $resultPage->addBreadcrumb(__('Pincode Finder'), __('Pincode Finder'));

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
