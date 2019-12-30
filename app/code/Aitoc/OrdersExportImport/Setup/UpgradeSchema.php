<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        
        $dbVersion = $context->getVersion();
        
        if (version_compare($dbVersion, '2.0.0', '<')) {
            $importTable = $setup->getTable('aitoc_import');
            
            $connection->dropColumn($importTable, 'error');
            
            $connection->addColumn(
                $importTable,
                'processed_count',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length'    => 10,
                    'nullable'  => false,
                    'default'   => 0,
                    'comment'   => 'Processed count'
                ]
            );
            
            $connection->addColumn(
                $importTable,
                'imported_count',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length'    => 10,
                    'nullable'  => false,
                    'default'   => 0,
                    'comment'   => 'Imported count'
                ]
            );
            
            $connection->dropTable($setup->getTable('aitoc_export_stack'));
            
            $connection->truncateTable($setup->getTable('aitoc_import'));
            $connection->truncateTable($setup->getTable('aitoc_export'));
            $connection->truncateTable($setup->getTable('aitoc_export_profile'));
            
            $exportTable = $setup->getTable('aitoc_export');
            
            $connection->dropColumn($exportTable, 'orders_count');
            $connection->dropColumn($exportTable, 'is_cron');
            $connection->dropColumn($exportTable, 'type_file');
            
            $connection->addColumn(
                $exportTable,
                'processed_count',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length'    => 10,
                    'nullable'  => false,
                    'default'   => 0,
                    'comment'   => 'Processed count'
                ]
            );
            
            $profileTable = $setup->getTable('aitoc_export_profile');

            $connection->dropColumn($profileTable, 'flag_auto');
            $connection->dropColumn($profileTable, 'crondate');
        }
        
        $setup->endSetup();
    }
}
