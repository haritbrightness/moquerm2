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
class OptionsCreditmemos extends StoreOptions
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
        $options  = [
            [
                'label' => __('Credit Memos'),
                'value' => 'fields'
            ],
            [
                'label' => __('Credit Memo Comments'),
                'value' => 'comment'
            ],
            [
                'label' => __('Credit Memo Items'),
                'value' => 'item'
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
