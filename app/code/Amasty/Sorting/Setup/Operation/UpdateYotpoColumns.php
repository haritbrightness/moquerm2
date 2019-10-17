<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;

class UpdateYotpoColumns
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute($setup)
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable(\Amasty\Sorting\Model\ResourceModel\Method\Toprated::INDEX_MAIN_TABLE);
        if ($connection->tableColumnExists($table, 'rating_summary')) {
            $connection->modifyColumn(
                $table,
                'rating_summary',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'   => '10,2',
                    'nullable' => false,
                    'unsigned' => true
                ]
            );
        }
    }
}
