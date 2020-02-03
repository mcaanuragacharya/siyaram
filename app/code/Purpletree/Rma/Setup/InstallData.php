<?php
/**
 * Purpletree_Rma InstallData
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
namespace Purpletree\Rma\Setup;
 
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
class InstallData implements InstallDataInterface
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
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->_date = $date;
        $this->_objectManager = $objectmanager;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        //Add Order Status starts
        /** @var \Magento\Sales\Model\Order\Status $status */
        $status = $this->_objectManager->create('Magento\Sales\Model\Order\Status');
        //status for state new
        $status->setData('status', 'pts_partial_return_initiated')->setData('label', 'Partial Return Initiated')->save();
        $status->assignState(\Magento\Sales\Model\Order::STATE_COMPLETE, false, true);
        //Add Order Status ends
        //status for state new
        $status->setData('status', 'pts_partial_return_completed')->setData('label', 'Partial Return Completed')->save();
        $status->assignState(\Magento\Sales\Model\Order::STATE_COMPLETE, false, true);
        //Add Order Status ends
        //status for state new
        $status->setData('status', 'pts_full_return_initiated')->setData('label', 'Full Return Initiated')->save();
        $status->assignState(\Magento\Sales\Model\Order::STATE_COMPLETE, false, true);
        //Add Order Status ends
        //status for state new
        $status->setData('status', 'pts_full_return_completed')->setData('label', 'Full Return Completed')->save();
        $status->assignState(\Magento\Sales\Model\Order::STATE_COMPLETE, false, true);
        //Add Order Status ends
         $setup->startSetup();
               // Resolution Rows Starts
        $dataResolutionRows = [
            [
                'pts_resolution_id' => 1,
                'pts_name' => 'Refund',
                'pts_enabled' => 1,
                'pts_sort_order' => 0,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_resolution_id' => 2,
                'pts_name' => 'Exchange',
                'pts_enabled' => 1,
                'pts_sort_order' => 1,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_resolution_id' => 3,
                'pts_name' => 'Cancel Items',
                'pts_enabled' => 1,
                'pts_sort_order' => 2,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ]
        ];
        
        foreach ($dataResolutionRows as $data) {
            $setup->getConnection()->insert($setup->getTable('purpletree_rma_resolution'), $data);
        }
               // Resolution Rows Ends
               // Package Condition Rows Starts
        $dataPackageConditionRows = [
            [
                'pts_packagecondition_id' => 1,
                'pts_name' => 'Unopened',
                'pts_enabled' => 1,
                'pts_sort_order' => 0,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_packagecondition_id' => 2,
                'pts_name' => 'Opened',
                'pts_enabled' => 1,
                'pts_sort_order' => 1,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_packagecondition_id' => 3,
                'pts_name' => 'Used',
                'pts_enabled' => 1,
                'pts_sort_order' => 2,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ]
        ];
        
        foreach ($dataPackageConditionRows as $data) {
            $setup->getConnection()->insert($setup->getTable('purpletree_rma_packagecondition'), $data);
        }
               // Package Condition Rows Ends
               // Status Rows Starts
        $dataStatusRows = [
            [
                'pts_status_id' => 1,
                'pts_name' => 'Pending',
                'pts_enabled' => 1,
                'pts_sort_order' => 0,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_status_id' => 2,
                'pts_name' => 'Received',
                'pts_enabled' => 1,
                'pts_sort_order' => 1,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_status_id' => 3,
                'pts_name' => 'Completed',
                'pts_enabled' => 1,
                'pts_sort_order' => 4,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_status_id' => 4,
                'pts_name' => 'On Hold',
                'pts_enabled' => 1,
                'pts_sort_order' => 3,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_status_id' => 5,
                'pts_name' => 'Processing',
                'pts_enabled' => 1,
                'pts_sort_order' => 2,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ]
        ];
        
        foreach ($dataStatusRows as $data) {
            $setup->getConnection()->insert($setup->getTable('purpletree_rma_status'), $data);
        }
               // Status Rows Ends
               // Reason Rows Starts
        $dataReasonRows = [
            [
                'pts_reason_id' => 1,
                'pts_name' => 'No Need',
                'pts_enabled' => 1,
                'pts_sort_order' => 0,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_reason_id' => 2,
                'pts_name' => 'Replacement',
                'pts_enabled' => 1,
                'pts_sort_order' => 1,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_reason_id' => 3,
                'pts_name' => 'Wrong Product',
                'pts_enabled' => 1,
                'pts_sort_order' => 2,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ],
            [
                'pts_reason_id' => 4,
                'pts_name' => 'Wrong Size',
                'pts_enabled' => 1,
                'pts_sort_order' => 3,
                'pts_created_at' => $this->_date->date(),
                'pts_updated_at' => $this->_date->date(),
            ]
        ];
        
        foreach ($dataReasonRows as $data) {
            $setup->getConnection()->insert($setup->getTable('purpletree_rma_reason'), $data);
        }
               // Reason Rows Ends
               $setup->endSetup();
    }
}
