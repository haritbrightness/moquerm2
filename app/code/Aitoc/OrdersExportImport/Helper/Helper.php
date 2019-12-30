<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Helper;

use \Magento\Framework\App\ResourceConnection;

/**
 * Class Helper
 */
class Helper extends \Magento\Framework\DB\Helper
{
    /**
     * @param $table
     * @return array
     */
    public function getFields($table)
    {
        $tableName = $this->_resource->getTableName($table);
        return array_keys($this->getConnection()->describeTable($tableName));
    }
}
