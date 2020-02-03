<?php
/**
 * Purpletree_Rma Uninstall
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
 
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
        //uninstall code;
        $setup->startSetup();
        
        $setup->getConnection()->dropTable($setup->getTable('purpletree_rma_return_order'));
        $setup->getConnection()->dropTable($setup->getTable('purpletree_rma_orderreturn_products'));
        $setup->getConnection()->dropTable($setup->getTable('purpletree_rma_reason'));
        $setup->getConnection()->dropTable($setup->getTable('purpletree_rma_status'));
        $setup->getConnection()->dropTable($setup->getTable('purpletree_rma_packagecondition'));
        $setup->getConnection()->dropTable($setup->getTable('purpletree_rma_resolution'));
 
        $setup->endSetup();
    }
}
