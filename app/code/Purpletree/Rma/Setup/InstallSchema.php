<?php
/**
 * Purpletree_Rma InstallSchema
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
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $purpletree_rma_orderreturnmessage = 'purpletree_rma_orderreturnmessage';
        $purpletree_rma_orderreturn_products = 'purpletree_rma_orderreturn_products';
        $purpletree_rma_orderreturn = 'purpletree_rma_orderreturn';
        $purpletree_rma_resolution = 'purpletree_rma_resolution';
        $purpletree_rma_packagecondition = 'purpletree_rma_packagecondition';
        $purpletree_rma_status = 'purpletree_rma_status';
        $purpletree_rma_reason = 'purpletree_rma_reason';
        //Remove Order Return Message Table
        if ($setup->getConnection()->isTableExists($purpletree_rma_orderreturnmessage) == true) {
            $setup->getConnection()->dropTable($setup->getTable($purpletree_rma_orderreturnmessage));
        }
        //Remove Order Return Message Table
        //Remove Order Return Product Table
        if ($setup->getConnection()->isTableExists($purpletree_rma_orderreturn_products) == true) {
            $setup->getConnection()->dropTable($setup->getTable($purpletree_rma_orderreturn_products));
        }
        //Remove Order Return Product Table
        //Remove Order Return Table
        if ($setup->getConnection()->isTableExists($purpletree_rma_orderreturn) == true) {
            $setup->getConnection()->dropTable($setup->getTable($purpletree_rma_orderreturn));
        }
        //Remove Order Return Table
        //Remove Resolution Table
        if ($setup->getConnection()->isTableExists($purpletree_rma_resolution) == true) {
            $setup->getConnection()->dropTable($setup->getTable($purpletree_rma_resolution));
        }
        //Remove Resolution Table
        //Remove Packagecondition Table
        if ($setup->getConnection()->isTableExists($purpletree_rma_packagecondition) == true) {
            $setup->getConnection()->dropTable($setup->getTable($purpletree_rma_packagecondition));
        }
        //Remove Packagecondition Table
        //Remove status Table
        if ($setup->getConnection()->isTableExists($purpletree_rma_status) == true) {
            $setup->getConnection()->dropTable($setup->getTable($purpletree_rma_status));
        }
        //Remove status Table
        //Remove reason Table
        if ($setup->getConnection()->isTableExists($purpletree_rma_reason) == true) {
            $setup->getConnection()->dropTable($setup->getTable($purpletree_rma_reason));
        }
        //Remove reason Table
        //Resolution Table starts
        $resolution_table = $setup->getConnection()->newTable(
            $setup->getTable($purpletree_rma_resolution)
        )->addColumn(
            'pts_resolution_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'pts_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'pts_enabled',
            \Magento\Framework\DB\Ddl\ Table::TYPE_BOOLEAN,
            null,
            ['unsigned' => true,'nullable' => false],
            'Enabled'
        )->addColumn(
            'pts_sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Sort Order'
        )->addColumn(
            'pts_created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'pts_updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->setComment(
            'Return Resolution Table'
        );
        $setup->getConnection()->createTable($resolution_table);
        //Resolution Table ends
        //Package Condition Table starts
        $packagecondition_table = $setup->getConnection()->newTable(
            $setup->getTable($purpletree_rma_packagecondition)
        )->addColumn(
            'pts_packagecondition_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'pts_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'pts_enabled',
            \Magento\Framework\DB\Ddl\ Table::TYPE_BOOLEAN,
            null,
            ['unsigned' => true,'nullable' => false],
            'Enabled'
        )->addColumn(
            'pts_sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Sort Order'
        )->addColumn(
            'pts_created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'pts_updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->setComment(
            'Return Package Condition Table'
        );
        $setup->getConnection()->createTable($packagecondition_table);
        //Package Condition Table ends
        //Status Table starts
        
        $status_table = $setup->getConnection()->newTable(
            $setup->getTable($purpletree_rma_status)
        )->addColumn(
            'pts_status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'pts_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'pts_enabled',
            \Magento\Framework\DB\Ddl\ Table::TYPE_BOOLEAN,
            null,
            ['unsigned' => true,'nullable' => false],
            'Enabled'
        )->addColumn(
            'pts_sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Sort Order'
        )->addColumn(
            'pts_created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'pts_updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->setComment(
            'Return Status Table'
        );
        $setup->getConnection()->createTable($status_table);
        //Status Table ends
        //Reason Table starts
        
        $reason_table = $setup->getConnection()->newTable(
            $setup->getTable($purpletree_rma_reason)
        )->addColumn(
            'pts_reason_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'pts_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'pts_enabled',
            \Magento\Framework\DB\Ddl\ Table::TYPE_BOOLEAN,
            null,
            ['unsigned' => true,'nullable' => false],
            'Enabled'
        )->addColumn(
            'pts_sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Sort Order'
        )->addColumn(
            'pts_created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'pts_updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->setComment(
            'Return Reason Table'
        );
        $setup->getConnection()->createTable($reason_table);
        //Reason Table ends
        //Order Return Table starts
        $orderreturn_table = $setup->getConnection()->newTable(
            $setup->getTable($purpletree_rma_orderreturn)
        )->addColumn(
            'pts_orderreturn_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'pts_order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Order ID'
        )->addColumn(
            'pts_status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Status ID'
        )->addColumn(
            'pts_packagecondition_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Package Condition ID'
        )->addColumn(
            'pts_reason_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Reason ID'
        )->addColumn(
            'pts_resolution_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Resolution ID'
        )->addColumn(
            'pts_created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'pts_updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $setup->getIdxName($purpletree_rma_orderreturn, ['pts_resolution_id']),
            ['pts_resolution_id']
        )->addIndex(
            $setup->getIdxName($purpletree_rma_orderreturn, ['pts_packagecondition_id']),
            ['pts_packagecondition_id']
        )->addIndex(
            $setup->getIdxName($purpletree_rma_orderreturn, ['pts_status_id']),
            ['pts_status_id']
        )->addIndex(
            $setup->getIdxName($purpletree_rma_orderreturn, ['pts_reason_id']),
            ['pts_reason_id']
        )->addForeignKey(
            $setup->getFkName(
                $purpletree_rma_orderreturn,
                'pts_resolution_id',
                $purpletree_rma_resolution,
                'pts_resolution_id'
            ),
            'pts_resolution_id',
            $setup->getTable($purpletree_rma_resolution),
            'pts_resolution_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_RESTRICT
        )->addForeignKey(
            $setup->getFkName(
                $purpletree_rma_orderreturn,
                'pts_packagecondition_id',
                $purpletree_rma_packagecondition,
                'pts_packagecondition_id'
            ),
            'pts_packagecondition_id',
            $setup->getTable($purpletree_rma_packagecondition),
            'pts_packagecondition_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_RESTRICT
        )->addForeignKey(
            $setup->getFkName(
                $purpletree_rma_orderreturn,
                'pts_status_id',
                $purpletree_rma_status,
                'pts_status_id'
            ),
            'pts_status_id',
            $setup->getTable($purpletree_rma_status),
            'pts_status_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_RESTRICT
        )->addForeignKey(
            $setup->getFkName(
                $purpletree_rma_orderreturn,
                'pts_reason_id',
                $purpletree_rma_reason,
                'pts_reason_id'
            ),
            'pts_reason_id',
            $setup->getTable($purpletree_rma_reason),
            'pts_reason_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_RESTRICT
        )->setComment(
            'Order Return Table'
        );
        $setup->getConnection()->createTable($orderreturn_table);
        //Order Return Table ends
        //Order Return Product Table starts
        $orderreturn_table = $setup->getConnection()->newTable(
            $setup->getTable($purpletree_rma_orderreturn_products)
        )->addColumn(
            'pts_orderreturn_product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'pts_orderreturn_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Order Return ID'
        )->addColumn(
            'pts_product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Product ID'
        )->addColumn(
            'pts_product_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Product Quantity'
        )->addIndex(
            $setup->getIdxName($purpletree_rma_orderreturn_products, ['pts_orderreturn_id']),
            ['pts_orderreturn_id']
        )->addForeignKey(
            $setup->getFkName(
                $purpletree_rma_orderreturn_products,
                'pts_orderreturn_id',
                $purpletree_rma_orderreturn,
                'pts_orderreturn_id'
            ),
            'pts_orderreturn_id',
            $setup->getTable($purpletree_rma_orderreturn),
            'pts_orderreturn_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Order Return Product Table'
        );
        $setup->getConnection()->createTable($orderreturn_table);
        //Order Return Product Table ends
        //Order Return Table Message Table starts
        $orderreturnmessage_table = $setup->getConnection()->newTable(
            $setup->getTable($purpletree_rma_orderreturnmessage)
        )->addColumn(
            'pts_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'pts_orderreturn_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Order Return ID'
        )->addColumn(
            'pts_status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => false],
            'Status ID'
        )->addColumn(
            'pts_orderreturn_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Order Return Message'
        )->addColumn(
            'pts_orderreturn_message_attachment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Order Return Message Attachement'
        )->addColumn(
            'pts_message_sender',
            \Magento\Framework\DB\Ddl\ Table::TYPE_BOOLEAN,
            null,
            ['unsigned' => true,'nullable' => false],
            'Message Sender'
        )->addColumn(
            'pts_created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addIndex(
            $setup->getIdxName($purpletree_rma_orderreturnmessage, ['pts_orderreturn_id']),
            ['pts_orderreturn_id']
        )->addForeignKey(
            $setup->getFkName(
                $purpletree_rma_orderreturnmessage,
                'pts_orderreturn_id',
                $purpletree_rma_orderreturn,
                'pts_orderreturn_id'
            ),
            'pts_orderreturn_id',
            $setup->getTable($purpletree_rma_orderreturn),
            'pts_orderreturn_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Order Return Message Table'
        );
        $setup->getConnection()->createTable($orderreturnmessage_table);
        //Order Return Table ends
        $setup->endSetup();
    }
}
