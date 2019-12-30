<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\ResourceModel\Export;

use Aitoc\OrdersExportImport\Model\Profile;

/**
 * Class Collection
 *
 * @package Aitoc\OrdersExportImport\Model\ResourceModel\Export
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'export_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aitoc\OrdersExportImport\Model\Export::class,
            \Aitoc\OrdersExportImport\Model\ResourceModel\Export::class
        );
    }
    
    /**
     * Add ready to export filter to collection
     *
     * @return $this
     */
    public function readyForExportFilter()
    {
        $this->addFieldToFilter(
            'status',
            [
                'in' => [
                    Profile::STATUS_PROCESSING,
                    Profile::STATUS_QUEUE
                ]
            ]
        );
        return $this;
    }
}
