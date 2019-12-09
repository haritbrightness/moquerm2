<?php
namespace Swissup\AddressFieldManager\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            /**
             * Create table 'swissup_afm_order_address'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('swissup_afm_order_address')
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Entity Id'
            )->addForeignKey(
                $installer->getFkName(
                    'swissup_afm_order_address',
                    'entity_id',
                    'sales_order_address',
                    'entity_id'
                ),
                'entity_id',
                $installer->getTable('sales_order_address'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Swissup Address Field Manager Order Address'
            );
            $installer->getConnection()->createTable($table);

            /**
             * Create table 'swissup_afm_quote_address'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('swissup_afm_quote_address')
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Entity Id'
            )->addForeignKey(
                $installer->getFkName(
                    'swissup_afm_quote_address',
                    'entity_id',
                    'quote_address',
                    'address_id'
                ),
                'entity_id',
                $installer->getTable('quote_address'),
                'address_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Swissup Address Field Manager Quote Address'
            );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
