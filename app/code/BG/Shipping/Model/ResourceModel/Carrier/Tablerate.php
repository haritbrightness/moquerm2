<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Shipping table rates
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace BG\Shipping\Model\ResourceModel\Carrier;

use Magento\Framework\Filesystem;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Import;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQuery;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQueryFactory;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @api
 * @since 100.0.2
 */

class Tablerate extends \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
{
    /**
     * Save import data batch
     *
     * @param array $data
     * @return \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
     */
    protected function _saveImportData(array $data)
    {
        if (!empty($data)) {
            $columns = [
                'website_id',
                'dest_country_id',
                'dest_region_id',
                'dest_zip',
                'condition_name',
                'condition_value',
                'price',
                'eta'
            ];
            $this->getConnection()->insertArray($this->getMainTable(), $columns, $data);
            $this->_importedRows += count($data);
        }

        return $this;
    }
}
