<?php

namespace Swissup\DeliveryDate\Setup;

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
        $installer = $setup;
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $installer->getConnection()
                ->modifyColumn(
                    $installer->getTable('swissup_deliverydate'),
                    'date',
                    [
                        'nullable' => true,
                        'default' => null,
                        'type' => Table::TYPE_DATETIME,
                        'comment' => 'Date',
                    ]
                );
        }

        $setup->endSetup();
    }
}
