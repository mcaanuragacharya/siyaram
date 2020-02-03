<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ortho\Bestsellerproduct\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
/**
 * Search helper
 */
class Data extends AbstractHelper
{

   
   /** * @var \Magento\Framework\App\Config\ScopeConfigInterfac */
   // protected $_scopeConfig;
    protected $_config;
    protected $_storeManager;
    protected $_productFactory;
	CONST FEATURED_ENABLE = 'bestseller_settings/general/isenable';
	CONST FEATURED_TITLE = 'bestseller_settings/general/title';
	CONST FEATURED_LIMIT = 'bestseller_settings/general/limit';
	CONST FEATURED_SIDEENABLE = 'bestseller_settings/general/isleftenable';
	CONST FEATURED_SIDELIMIT = 'bestseller_settings/general/sidebarlimit';
	CONST FEATURED_METATITLE = 'bestseller_settings/bestseller_metadata/meta_title';
	CONST FEATURED_METAKEYWORD = 'bestseller_settings/bestseller_metadata/meta_keyword';
	CONST FEATURED_MTEADESC = 'bestseller_settings/bestseller_metadata/meta_description';
	
	
    /**
     * Initialize
     *
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Catalog\Model\ProductFactory $productFactory
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
    	\Magento\Framework\App\Helper\Context $context, 
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager 
		// array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
	//	$this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

   	public function getBestsellerstatus()
    {
        return $this->scopeConfig->getValue(self::FEATURED_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getBestsellerlimit()
    {
        return $this->scopeConfig->getValue(self::FEATURED_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getBestsellerTitle()
    {
		///echo 'check';
        return $this->scopeConfig->getValue(self::FEATURED_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getBestsellerleftstatus()
    {
        return $this->scopeConfig->getValue(self::FEATURED_SIDEENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getBestsellerleftlimit()
    {
        return $this->scopeConfig->getValue(self::FEATURED_SIDELIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	
	public function getMetaTitle()
    {
        return $this->scopeConfig->getValue(self::FEATURED_METATITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	
	public function getMetaKeyword()
    {
        return $this->scopeConfig->getValue(self::FEATURED_METAKEYWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	
	public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(self::FEATURED_MTEADESC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	
	
    public function getBreadcrumbs(\Magento\Framework\View\Result\Page $resultPage) {
       
	   	///echo 'check breadcumbs';
	    $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
       
	    $breadcrumbs->addCrumb(
             'home', [
            'label' => __('Home'),
            'title' => __('Home Page'),
            'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
        );
        $breadcrumbs->addCrumb(
                'cms_page', ['label' => __('Bestseller Product'), 'title' => __('Bestseller Product')]
        );
    }
	

}
