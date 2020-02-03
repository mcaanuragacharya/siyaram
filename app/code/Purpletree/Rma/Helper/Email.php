<?php
/**
 * Purpletree_Rma Email
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
namespace Purpletree\Rma\Helper;

/**
 * Custom Module Email helper
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_MESSAGETEMPLATE = 'Hello _Name_, Your request for order no. _ORDER_ has recieved message from admin. Track your request at _URL_';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
 
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;
 
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    
    protected $_order;
    
    protected $_orderReturnModel;
    
    protected $_status;
    
    protected $_dataHelper;
    
    protected $_processData;
    
    /**
     * @param Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
     * @param \Magento\Sales\Model\Order $order,
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
     * @param \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderReturnModel,
     * @param \Purpletree\Rma\Model\ResourceModel\Status $status
     * @param \Purpletree\Rma\Helper\Data $dataHelper,
     * @param \Purpletree\Rma\Helper\Processdata $processData,
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Sales\Model\Order $order,
        \Purpletree\Rma\Model\ResourceModel\Orderreturn $orderReturnModel,
        \Purpletree\Rma\Model\ResourceModel\Status $status,
        \Purpletree\Rma\Helper\Data $dataHelper,
        \Purpletree\Rma\Helper\Processdata $processData
    ) {
        $this->_scopeConfig      = $context;
        $this->_storeManager     = $storeManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_order            = $order;
        $this->_orderReturnModel = $orderReturnModel;
        $this->_status           = $status;
        $this->_dataHelper       = $dataHelper;
        $this->_processData      = $processData;
        parent::__construct($context);
    }
 
    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
 
    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
 
    /**
     * [generateTemplate description]  with template file and tempaltes variables values
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return void
     */
	 	public function sendEmail($emailTemplateVariables,$senderInfo,$receiverInfo,$identifier)
    {
        $this->temp_id = $identifier;
        $this->_inlineTranslation->suspend();    
		$this->generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();        
        $this->_inlineTranslation->resume();
    }
	 public function generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo)
    {
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, /* here you can defile area and
                                                                                 store of template for which you prepare it */
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($senderInfo)
                ->addTo($receiverInfo['email'],$receiverInfo['name']);
        return $this;        
    }
 
    /**
     * [sendInvoicedOrderEmail description]
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return void
     */
    /* your send mail method*/
    public function emailCustomer($orderreturnId, $new_status_id, $created_date, $identifier, $short_message = null, $sender = 0)
    {
        $orderid = $this->_orderReturnModel->getOrderIdById($orderreturnId);
        $order   = $this->_order->loadByIncrementId($orderid);
        if (null !== $order) {
            $adminemaildata = $this->_dataHelper->getGeneralConfig('/email_configuration/email_id');
            $adminEmail = $this->_dataHelper->getStoreEmail($adminemaildata);
            $adminName = $this->_dataHelper->getStoreEmailName($adminemaildata);
            $status_name  = $this->_status->getStatusNameById($new_status_id);
            if ($order->getCustomerId()) {
                $customerEmail = $order->getCustomerEmail();
                $customerName = $order->getCustomerFirstname()." ".$order->getCustomerLastname();
            } else {
                $customerEmail = $order->getBillingAddress()->getEmail();
                $customerName = $order->getBillingAddress()->getFirstname().' '.$order->getBillingAddress()->getLastname();
            }
            if ($sender == 0) {
                /* Receiver Detail  */
                $receiverInfo = [
                'name' => $customerName,
                'email' => $customerEmail
                ];
                /* Sender Detail  */
                $senderInfo = [
                'name' => $adminName,
                'email' => $adminEmail,
                ];
            } else {
                /* Receiver Detail  */
                $receiverInfo = [
                'name' => $adminName,
                'email' => $adminEmail,
                ];
                /* Sender Detail  */
                $senderInfo = $receiverInfo;
            }
                /* Assign values for your template variables  */
                    $emailTempVariables = [];
                    $emailTempVariables['customer_name']    = $customerName;
                    $emailTempVariables['customer_email']   = $customerEmail;
                    $emailTempVariables['status_name']      = $status_name;
                    $emailTempVariables['order_id']         = $orderid;
                    $emailTempVariables['created_date']     = $created_date;
                    $emailTempVariables['short_message']    = $short_message;
                    $emailTempVariables['adminName']    = $adminName;
                    $emailTempVariables['website_title']    = $this->_storeManager->getStore()->getBaseUrl();
                    $emailTempVariables['customer_link']    = '<a href="'.$this->_storeManager->getStore()->getBaseUrl().'rma/index/view/id/'.$orderreturnId.'">Click Here</a>';
                    
                   // $emailTempVariables['website_title'] = $this->_storeManager->getStore()->getBaseUrl();
                    /* call send mail method from helper or where you define it*/
                    $this->sendEmail(
                        $emailTempVariables,
                        $senderInfo,
                        $receiverInfo,
                        $identifier
                    );
        }
    }
    public function smsToCustomer($orderreturnId, $new_status_id = null, $sms)
    {
        $status_name  = $this->_status->getStatusNameById($new_status_id);
        $orderid = $this->_orderReturnModel->getOrderIdById($orderreturnId);
        $order   = $this->_order->loadByIncrementId($orderid);
        if ($order) {
                     $customerTelephone = $order->getBillingAddress()->getTelephone();
            if ($order->getCustomerId()) {
                $customerName = $order->getCustomerFirstname()." ".$order->getCustomerLastname();
            } else {
                $customerName = $order->getBillingAddress()->getFirstname().' '.$order->getBillingAddress()->getLastname();
            }
                    $patterns       = ['/_NAME_/','/_ORDERID_/','/_URL_/','/_STATUS_/'];
                    $storeurl       = $this->_storeManager->getStore()->getBaseUrl();
                    $replacements   = [$customerName, $orderid, $storeurl, $status_name ];
                    $messageNormal  = preg_replace($patterns, $replacements, $sms);
                    $message        = urlencode($messageNormal);
                    $apiUrl         = $this->_dataHelper->getGeneralConfig('/smsapi/url_api');
                    $patternsApi    = ['/_TEXT_/','/_MOB_/'];
                    $replacementsApi= [$message,$customerTelephone];
                    $smsApiUrl      = preg_replace($patternsApi, $replacementsApi, $apiUrl);
                    return $this->_processData->curlsms($smsApiUrl);
        }
    }
}
