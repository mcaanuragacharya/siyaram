<?php
/**
 * Purpletree_Rma Orderreturn
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
namespace Purpletree\Rma\Model\ResourceModel;

class Orderreturn extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * constructor
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
    
        $this->_date = $date;
        parent::__construct($context);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('purpletree_rma_orderreturn', 'pts_orderreturn_id');
    }

    /**
     * Retrieves Order ID from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */
    public function getOrderIdById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'pts_order_id')
            ->where('pts_orderreturn_id = :pts_orderreturn_id');
        $binds = ['pts_orderreturn_id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }
    public function getOrderreturnIdByOrderId($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'pts_orderreturn_id')
            ->where('pts_order_id = :pts_order_id');
        $binds = ['pts_order_id' => $id];
        return $adapter->fetchOne($select, $binds);
    }
    public function getStatusIdById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'pts_status_id')
            ->where('pts_orderreturn_id = :pts_orderreturn_id');
        $binds = ['pts_orderreturn_id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }
    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Purpletree\Rma\Model\Orderreturn $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setPtsUpdatedAt($this->_date->date());
        if ($object->isObjectNew()) {
            $object->setPtsCreatedAt($this->_date->date());
        }
        return parent::_beforeSave($object);
    }
}
