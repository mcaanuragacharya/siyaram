<?php
/**
 * Purpletree_Rma ListMode
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

//Create Status option
class ListMode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $manager = \Magento\Framework\App\ObjectManager::getInstance();
        $statusList = $manager->create('\Magento\Sales\Model\Order\Config');
        return $statuses = $statusList->getStateStatuses('complete', $addLabels = true);
    }
}
