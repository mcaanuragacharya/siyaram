<?php
namespace Pixeltoweb\Importexport\Controller\Adminhtml\Importexport;

use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
            Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {       
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Import Products / Import Categories / Export Orders'));
        return  $resultPage;
    }

}