<?php
/**
 * Purpletree_Rma AdminEmails
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

class AdminEmails implements ArrayInterface
{
    public function toOptionArray()
    {
          $ret[] = ['value' => 'admin_initiate_email','label' => __('Return Request Initiated')];
          $ret[] = ['value' => 'admin_comment_email','label' => __('Message added by Customer')];
          return $ret;
    }
}
