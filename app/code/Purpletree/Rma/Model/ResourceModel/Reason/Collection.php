<?php
/**
 * Purpletree_Rma Collection
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
namespace Purpletree\Rma\Model\ResourceModel\Reason;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'pts_reason_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'purpletree_rma_reason_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'reason_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Purpletree\Rma\Model\Reason', 'Purpletree\Rma\Model\ResourceModel\Reason');
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */

    public function _toOptionArray($valueField = 'pts_reason_id', $labelField = 'pts_name', $additional = ['pts_enabled' => 'pts_enabled'])
    {
        $res = [];
        if (!empty(parent::_toOptionArray($valueField, $labelField, $additional))) {
            foreach (parent::_toOptionArray($valueField, $labelField, $additional) as $items) {
                if (!empty($items)) {
                    if ($items['pts_enabled'] == 1) {
                        $res[] = [
                        'label' => $items['label'],
                        'value' => $items['value']
                        ];
                    }
                }
            }
        }
        return $res;
    }
}
