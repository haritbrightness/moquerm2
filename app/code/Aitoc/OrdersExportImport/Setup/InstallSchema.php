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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_export_profile'))
            ->addColumn(
                'profile_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Profile ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                '255',
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'config',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Parameters'
            )
            ->addColumn(
                'date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Date'
            )
            ->setComment('Export Profile');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_export'))
            ->addColumn(
                'export_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Export ID'
            )
            ->addColumn(
                'profile_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'Profile ID'
                ]
            )
            ->addColumn(
                'dt',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'DateTime'
            )
            ->addColumn(
                'filename',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'FileName'
            )
            ->addColumn(
                'serialized_config',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Parameters'
            )
            ->addColumn(
                'processed_count',
                Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Orders Count'
            )
            ->addColumn(
                'status',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false],
                'Status'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_export',
                    'profile_id',
                    'aitoc_export_profile',
                    'profile_id'
                ),
                'profile_id',
                $installer->getTable('aitoc_export_profile'),
                'profile_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Table Export');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_import'))
            ->addColumn(
                'import_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Import ID'
            )
            ->addColumn(
                'filename',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'FileName'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => false, 'default' => 0],
                'Status'
            )
            ->addColumn(
                'serialized_config',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Parameters'
            )
            ->addColumn(
                'dt',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'DateTime'
            )
            ->addColumn(
                'processed_count',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false, 'default' => 0],
                'Processed count'
            )
            ->addColumn(
                'imported_count',
                Table::TYPE_SMALLINT,
                10,
                ['nullable' => false, 'default' => 0],
                'Imported count'
            )
            ->setComment('Table Import');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
