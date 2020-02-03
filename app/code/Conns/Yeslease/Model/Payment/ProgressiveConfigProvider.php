<?php
/**
 * Copyright © Conn's. All rights reserved.
 */
namespace Conns\Yeslease\Model\Payment;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Conns\Locator\Helper\Data as LocatorHelper;
use Conns\Yeslease\Helper\Data as ProgressiveHelper;
use Magento\Framework\View\LayoutInterface;

/**
 * Class CreditConfigProvider
 * @package Conns\Credit\Model\Payment
 */
class ProgressiveConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCode = 'progressive';

    /**
     * @var Credit
     */
    protected $method;
	
	/**
     * @var ProgressiveHelper
     */
    protected $progressiveHelper;

    /**
     * @var Escaper
     */
    protected $escaper;
 
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;
	
	/**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
	
	protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected $locatorHelper;
	
	private $storeManager;


    /**
     * CreditConfigProvider constructor.
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param LayoutInterface $layout
     * @param Escaper $escaper
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        LayoutInterface $layout,
		PaymentHelper $paymentHelper,
        Escaper $escaper,
		ProgressiveHelper $progressiveHelper,
		\Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        LocatorHelper $locatorHelper
    ) {
        $this->assetRepo = $assetRepo;
        $this->layout = $layout;
		$this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->locatorHelper = $locatorHelper;
		$this->progressiveHelper = $progressiveHelper;
		$this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
		$this->_scopeConfig = $scopeConfig;
		$this->method = $paymentHelper->getMethodInstance($this->methodCode);
        
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                'progressive' => [
                    'image' => $this->getIconImage(),
                    'autoLeaseNumberForCheckout' => $this->getAutoLeaseNumberForCheckout(),
					'openEyeImage' => $this->getImageFileUrl('open-eye.png'),
                    'closeEyeImage' => $this->getImageFileUrl('closed-eye.png')
                ],
            ],
        ] : [];
    }

    /**
     * @return mixed
     */
    protected function getIconImage(){
        $path = $this->_scopeConfig->getValue(
            'payment/progressive/payment_icon',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        return $mediaUrl.'yeslease/image/'.$path;
    }
	
	/*
	*******
	Auto Populate Lease Number on checkout Session
	*******
	*/
	protected function getAutoLeaseNumberForCheckout(){
        return $this->_customerSession->getAutoLeaseNumberOnCheckout();
    }

    

    /**
     * @param $imageFileName
     * @return string
     */
    protected function getImageFileUrl($imageFileName){
        return $this->assetRepo->getUrl('Conns_Credit::images/'.$imageFileName);
    }

    
}
