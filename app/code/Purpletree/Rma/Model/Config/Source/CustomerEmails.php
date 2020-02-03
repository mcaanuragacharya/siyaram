<?php
/**
 * Purpletree_Rma CustomerEmails
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
namespace Purpletree\Rma\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CustomerEmails implements ArrayInterface
{
    public function toOptionArray()
    {
          $ret[] = ['value' => 'customer_initiate_email','label' => __('Return Request Initiated')];
          $ret[] = ['value' => 'customer_status_change_email','label' => __('Status Changed by Admin')];
          $ret[] = ['value' => 'customer_comment_email','label' => __('Message added by Admin')];
          return $ret;
    }
}
