<?php
/**
 * Purpletree_Rma Data
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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    const DEFAULT_ENABLED  =   0;
    
    const XML_PATH_RMA = 'purpletree_rma';
    
    const DEFAULT_INITIATE_TEMPLATE = 'Hello _NAME_, Your request for order no. _ORDERID_ has been received. Track your request at _URL_';
    const DEFAULT_STATUS_TEMPLATE = 'ello _NAME_, Your request for order no. _ORDERID_ is _STATUS_. Track your request at _URL_';
    const DEFAULT_MESSAGE_TEMPLATE = 'Hello _Name_, Your request for order no. _ORDER_ has recieved message from admin. Track your request at _URL_';

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RMA . $code, $storeId);
    }
    public function isEnabled($storeId = null)
    {
        $data = $this->getGeneralConfig('/general/enabled', $storeId);
        if ($data == null) {
            $data = self::DEFAULT_ENABLED;
        }
        return $data;
    }
    public function messageTemplate($storeId = null)
    {
        $data = $this->getGeneralConfig('/smsapi/sms_rma_comment_template', $storeId);
        if ($data == null) {
            $data = self::DEFAULT_MESSAGE_TEMPLATE;
        }
        return $data;
    }
    public function initiateTemplate($storeId = null)
    {
        $data = $this->getGeneralConfig('/smsapi/sms_rma_initiate_template', $storeId);
        if ($data == null) {
            $data = self::DEFAULT_INITIATE_TEMPLATE;
        }
        return $data;
    }
    public function statusTemplate($storeId = null)
    {
        $data = $this->getGeneralConfig('/smsapi/sms_rma_status_change_template', $storeId);
        if ($data == null) {
            $data = self::DEFAULT_STATUS_TEMPLATE;
        }
        return $data;
    }
        /**
         * Admin Store Email
         *
         * @return  Admin Store Email
         */
    public function getStoreEmail($from)
    {
        return $this->getConfigValue('trans_email/ident_'.$from.'/email');
		
    }
    public function getStoreEmailName($from)
    {
		return $this->getConfigValue('trans_email/ident_general/name');
    }
}
