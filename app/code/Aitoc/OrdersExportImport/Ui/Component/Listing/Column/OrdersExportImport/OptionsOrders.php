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
class OptionsOrders extends StoreOptions
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
                'label' => __('Order Items'),
                'value' => 'item'
            ],
            [
                'label' => __('Order Addresses'),
                'value' => 'address'
            ],
            [
                'label' => __('Order Payments'),
                'value' => 'payment'
            ],
            [
                'label' => __('Order Payment Transactions'),
                'value' => 'paymenttransaction'
            ],
            [
                'label' => __('Order Status History'),
                'value' => 'statushistory'
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
