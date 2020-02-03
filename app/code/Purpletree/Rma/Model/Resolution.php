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
namespace Purpletree\Rma\Model;

/**
 * @method Resolution setName($name)
 * @method mixed getPtsName()
 * @method Resolution setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Resolution setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
class Resolution extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'purpletree_rma_resolution';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'purpletree_rma_resolution';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'purpletree_rma_resolution';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Purpletree\Rma\Model\ResourceModel\Resolution');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getPtsResolutionId()];
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
