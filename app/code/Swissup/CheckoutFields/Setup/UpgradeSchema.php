<?php
namespace Swissup\CheckoutFields\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $connection->dropForeignKey(
                $setup->getTable('swissup_checkoutfields_store'),
                $setup->getFkName(
                    'swissup_checkoutfields_store',
                    'field_id',
                    'swissup_checkoutfields_field',
                    'field_id'
                )
            );

            $connection->modifyColumn(
                $setup->getTable('swissup_checkoutfields_store'),
                'field_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'identity' => false,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'comment' => 'Field Id'
                ]
            );

            $connection->dropIndex(
                $setup->getTable('swissup_checkoutfields_store'),
                'PRIMARY'
            );

            $connection->addIndex(
                $setup->getTable('swissup_checkoutfields_store'),
                'PRIMARY',
                ['field_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
            );

            $connection->addForeignKey(
                $setup->getFkName('swissup_checkoutfields_store', 'field_id', 'swissup_checkoutfields_field', 'field_id'),
                $setup->getTable('swissup_checkoutfields_store'),
                'field_id',
                $setup->getTable('swissup_checkoutfields_field'),
                'field_id',
                Table::ACTION_CASCADE
            );
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $connection->addColumn(
                $setup->getTable('swissup_checkoutfields_field'),
                'is_used_in_grid',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Is Used in Grid'
                ]
            );
        }

        $setup->endSetup();
    }
}
