<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace BG\Shipping\Block\Adminhtml\Carrier\Tablerate;

/**
 * Shipping carrier table rate grid block
 * WARNING: This grid used for export table rates
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\OfflineShipping\Block\Adminhtml\Carrier\Tablerate\Grid {

    /**
     * Prepare table columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns() {
        $this->addColumn(
                'dest_country',
                ['header' => __('Country'), 'index' => 'dest_country', 'default' => '*']
        );

        $this->addColumn(
                'dest_region',
                ['header' => __('Region/State'), 'index' => 'dest_region', 'default' => '*']
        );

        $this->addColumn(
                'dest_zip',
                ['header' => __('Zip/Postal Code'), 'index' => 'dest_zip', 'default' => '*']
        );

        $label = $this->_tablerate->getCode('condition_name_short', $this->getConditionName());
        $this->addColumn('condition_value', ['header' => $label, 'index' => 'condition_value']);

        $this->addColumn('price', ['header' => __('Shipping Price'), 'index' => 'price']);
        
        // Newly added
        $this->addColumn('eta', ['header' => __('Delivery time in business days'), 'index' => 'eta']);
        
        $this->addColumnsOrder('eta', 'price');

        return parent::_prepareColumns();
    }

}
