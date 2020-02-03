<?php
/**
 * Purpletree_Rma Status
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

class Status implements \Magento\Framework\Option\ArrayInterface
{
    protected $_statusOptions;
    
    public function __construct(
        \Purpletree\Rma\Model\ResourceModel\Status\Collection $statusOptions,
        array $data = []
    ) {
        $this->_statusOptions     = $statusOptions;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_statusOptions->_toOptionArray();
    }
}
