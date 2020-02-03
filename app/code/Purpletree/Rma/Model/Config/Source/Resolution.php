<?php
/**
 * Purpletree_Rma Resolution
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

class Resolution implements \Magento\Framework\Option\ArrayInterface
{
    protected $_resolutionOptions;
    
    public function __construct(
        \Purpletree\Rma\Model\ResourceModel\Resolution\Collection $resolutionOptions,
        array $data = []
    ) {
        $this->_resolutionOptions     = $resolutionOptions;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_resolutionOptions->_toOptionArray();
    }
}
