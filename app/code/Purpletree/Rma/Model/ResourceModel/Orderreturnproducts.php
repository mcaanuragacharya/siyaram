<?php
/**
 * Purpletree_Rma Orderreturnproducts
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

class Orderreturnproducts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('purpletree_rma_orderreturn_products', 'pts_orderreturn_product_id');
    }
    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Purpletree\Rma\Model\Orderreturnproducts $object
     * @return $this
     */
    public function getProducts($returnId)
    {
        $adapter = $this->getConnection();
        $sql = $adapter->select()
        ->from($this->getMainTable())
              ->where('pts_orderreturn_id = ?', $returnId);
        return $adapter->fetchAll($sql);
    }
}
