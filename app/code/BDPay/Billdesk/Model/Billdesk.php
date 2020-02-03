<?php

namespace BDPay\Billdesk\Model;

class Billdesk extends \Magento\Payment\Model\Method\AbstractMethod
{
	const PAYMENT_METHOD_BILLDESK_CODE = 'billdesk';
	const BILLDESK_TITLE = 'title';
	const BILLDESK_MERCHANT_ID = 'merchant_id';
	const BILLDESK_ENCRYPTION_KEY = 'encryption_key';

    protected $_code = self::PAYMENT_METHOD_BILLDESK_CODE;
    protected $_storeScope =  \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

  	public function getTitle() 
	{
		return $this->getConfigData(self::BILLDESK_TITLE);
	}

	

	public function getMerchantId()
	{
	 	return $this->getConfigData(self::BILLDESK_MERCHANT_ID);
	}

	public function getEncryptionKey()
	{
 		return $this->getConfigData(self::BILLDESK_ENCRYPTION_KEY);
	}


	public function getRequestUrl(){

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$url = $objectManager->get('\Magento\Framework\UrlInterface');
		print_r($url->getUrl('billdesk/request'));
	}

	public function getResponseUrl(){

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$url = $objectManager->get('\Magento\Framework\UrlInterface');
		return ($url->getUrl('billdesk/response'));
	}

	public function getCountryName($countryId){

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$countryModel = $objectManager->get('\Magento\Directory\Model\Country'); 
		$countryModel->loadByCode($countryId);

     	return $countryModel->getName();
	}

	public function getQuote(){

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 

		if($cart->getQuote()->getIsActive())
			return $cart->getQuote();
		else
			return NULL;
	}

	public function getEncryptedData(){
		$quote = $this->getQuote();
		$quote->getPayment()->setMethod('billdesk');

        //Configuration
        $encryption_key     = trim($this->getEncryptionKey());
        $billdesk_title     = trim($this->getTitle());

		//General
        $merchant_id        = trim($this->getMerchantId());
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$cartManager = $objectManager->get('\Magento\Quote\Api\CartManagementInterface');
		$order_id = $cartManager->placeOrder($quote->getId());
		

        $currency           = trim($quote->getQuoteCurrencyCode());
        $payment_total      = (float) $quote->getGrandTotal();
        
        $language           = 'EN';

        //URL
        $Redirect_Url       = $this->getResponseUrl();
        $Cancel_Url         = $this->getResponseUrl();
		
		$billing_address    = $quote->getBillingAddress()->getData();
        $billing_email      = $billing_address['email'];
    	
        $delivery_address   = $quote->getShippingAddress()->getData();
         
		$str = $merchant_id."|".$order_id."|NA|".$payment_total."|NA|NA|NA|INR|NA|R|".strtolower($merchant_id)."|NA|NA|F|".$billing_email."|NA|NA|NA|NA|NA|NA|".$Redirect_Url;

		$checksum = hash_hmac('sha256',$str,$encryption_key, false); 
		$checksum = strtoupper($checksum);
		$str .='|'.$checksum;
		$msg=$str;
		
       return $msg;
   
	}

   
    
}
