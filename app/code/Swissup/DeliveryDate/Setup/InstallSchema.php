<?php
namespace Swissup\DeliveryDate\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'swissup_deliverydate'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_deliverydate')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'quote_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true, 'default'  => null],
            'Quote ID'
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true, 'default'  => null],
            'Order ID'
        )->addColumn(
            'comment',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default'  => ''],
            'Comment'
        )->addColumn(
            'date',
            Table::TYPE_DATETIME,
            null,
            ['nullable' => true, 'default'  => null],
            'Date'
        )->addColumn(
            'timerange',
            Table::TYPE_TEXT,
            13,
            ['nullable' => true, 'default'  => null],
            'Time range'
        )->addIndex(
            $installer->getIdxName('swissup_deliverydate', ['quote_id']),
            ['quote_id']
        )->addIndex(
            $installer->getIdxName('swissup_deliverydate', ['order_id']),
            ['order_id']
        )->addForeignKey(
            $installer->getFkName('swissup_deliverydate', 'quote_id', 'quote', 'entity_id'),
            'quote_id',
            $installer->getTable('quote'),
            'entity_id',
            Table::ACTION_SET_NULL
        )->addForeignKey(
            $installer->getFkName('swissup_deliverydate', 'order_id', 'sales_order', 'entity_id'),
            'order_id',
            $installer->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Swissup Delivery Date Table'
        );
        $installer->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
