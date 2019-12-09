<?php
namespace Swissup\CheckoutFields\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'swissup_checkoutfields_field'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_checkoutfields_field')
        )->addColumn(
            'field_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Id'
        )->addColumn(
            'attribute_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Attribute Code'
        )->addColumn(
            'frontend_input',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Frontend Input'
        )->addColumn(
            'frontend_label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Frontend Label'
        )->addColumn(
            'is_required',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Defines Is Required'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Sort Order'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Defines Is Entity Active'
        )->addColumn(
            'default_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Default Value'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addColumn(
            'is_used_in_grid',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is Used in Grid'
        )->addIndex(
            $installer->getIdxName(
                'swissup_checkoutfields_field',
                ['attribute_code'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['attribute_code'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Swissup Checkout Fields Field Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_checkoutfields_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_checkoutfields_store')
        )->addColumn(
            'field_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0', 'primary' => true],
            'Store Id'
        )->addIndex(
            $installer->getIdxName('swissup_checkoutfields_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_store', 'field_id', 'swissup_checkoutfields_field', 'field_id'),
            'field_id',
            $installer->getTable('swissup_checkoutfields_field'),
            'field_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Swissup Checkout Field To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_checkoutfields_field_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_checkoutfields_field_label')
        )->addColumn(
            'field_label_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Label Id'
        )->addColumn(
            'field_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Field Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Value'
        )->addIndex(
            $installer->getIdxName('swissup_checkoutfields_field_label', ['store_id']),
            ['store_id']
        )->addIndex(
            $installer->getIdxName('swissup_checkoutfields_field_label', ['field_id', 'store_id']),
            ['field_id', 'store_id']
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_field_label', 'field_id', 'swissup_checkoutfields_field', 'field_id'),
            'field_id',
            $installer->getTable('swissup_checkoutfields_field'),
            'field_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_field_label', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Checkout Field Label'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_checkoutfields_field_option'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_checkoutfields_field_option')
        )->addColumn(
            'option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Option Id'
        )->addColumn(
            'field_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Field Id'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Sort Order'
        )->addIndex(
            $installer->getIdxName('swissup_checkoutfields_field_option', ['field_id']),
            ['field_id']
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_field_option', 'field_id', 'swissup_checkoutfields_field', 'field_id'),
            'field_id',
            $installer->getTable('swissup_checkoutfields_field'),
            'field_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Checkout Field Option'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_checkoutfields_field_option_value'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_checkoutfields_field_option_value')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Option Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Value'
        )->addIndex(
            $installer->getIdxName('swissup_checkoutfields_field_option_value', ['option_id']),
            ['option_id']
        )->addIndex(
            $installer->getIdxName('swissup_checkoutfields_field_option_value', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_field_option_value', 'option_id', 'swissup_checkoutfields_field_option', 'option_id'),
            'option_id',
            $installer->getTable('swissup_checkoutfields_field_option'),
            'option_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_field_option_value', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Checkout Field Option Value'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_checkoutfields_values'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_checkoutfields_values')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'field_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Field Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Quote Id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Order Id'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Field Value'
        )->addIndex(
            $installer->getIdxName('swissup_checkoutfields_values', ['store_id']),
            ['store_id']
        )->addIndex(
            $installer->getIdxName('swissup_checkoutfields_values', ['field_id', 'value']),
            ['field_id', 'value']
        )->addIndex(
            $installer->getIdxName(
                'swissup_checkoutfields_values',
                ['quote_id', 'field_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['quote_id', 'field_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName(
                'swissup_checkoutfields_values',
                ['order_id', 'field_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['order_id', 'field_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_values', 'quote_id', 'quote', 'entity_id'),
            'quote_id',
            $installer->getTable('quote'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_values', 'order_id', 'sales_order', 'entity_id'),
            'order_id',
            $installer->getTable('sales_order'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_values', 'field_id', 'swissup_checkoutfields_field', 'field_id'),
            'field_id',
            $installer->getTable('swissup_checkoutfields_field'),
            'field_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_checkoutfields_values', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Checkout Field Values'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
