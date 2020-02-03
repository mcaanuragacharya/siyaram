<?php
/**
 * Purpletree_Rma Recent
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
namespace Purpletree\Rma\Plugin\Order;
 
class Recent
{
    public function beforeToHtml(\Magento\Sales\Block\Order\Recent $template)
    {
            $template->setTemplate('Purpletree_Rma::order/recent.phtml');
    }
}
