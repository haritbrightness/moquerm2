<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Column\OrdersExportImport;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;
use Aitoc\OrdersExportImport\Model\Profile;

/**
 * Store Options for Cms Pages and Blocks
 */
class OptionsExport extends StoreOptions
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
            '0' => [
                'label' => __('Manual export from order grid'),
                'value' => Profile::TYPE_MANUAL
            ],
            '1' => [
                'label' => __('Automatically after checkout'),
                'value' => Profile::TYPE_CHECKOUT
            ],
            '2' => [
                'label' => __('Automatically once invoices for all products in an order are created'),
                'value' => Profile::TYPE_INVOICE
            ],
        ];
        
        $this->options = $options;

        return $this->options;
    }
}
