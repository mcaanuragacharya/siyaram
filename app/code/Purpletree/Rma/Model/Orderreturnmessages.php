<?php
/**
 * Purpletree_Rma Orderreturnmessages
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
namespace Purpletree\Rma\Model;

/**
 * @method Orderreturnmessages setName($name)
 * @method mixed getPtsName()
 * @method Orderreturnmessages setPtsCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Orderreturnmessages setPtsUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
class Orderreturnmessages extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'purpletree_rma_orderreturnmessages';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'purpletree_rma_orderreturnmessages';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'purpletree_rma_orderreturnmessages';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Purpletree\Rma\Model\ResourceModel\Orderreturnmessages');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getPtsEntityId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
