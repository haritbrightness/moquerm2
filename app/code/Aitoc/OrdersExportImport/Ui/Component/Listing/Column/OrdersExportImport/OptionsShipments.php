<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Column\OrdersExportImport;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Store Options for Cms Pages and Blocks
 */
class OptionsShipments extends StoreOptions
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $options = [
            [
                'label' => __('Shipments'),
                'value' => 'fields'
            ],
            [
                'label' => __('Shipment Comments'),
                'value' => 'comment'
            ],
            [
                'label' => __('Shipped Items'),
                'value' => 'item'
            ],
            [
                'label' => __('Shipments Tracking Information'),
                'value' => 'trackinginformation'
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
