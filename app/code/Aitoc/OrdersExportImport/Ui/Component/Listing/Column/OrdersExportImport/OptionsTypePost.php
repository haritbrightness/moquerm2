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
class OptionsTypePost extends StoreOptions
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
        
        $this->options = [
            '0' => [
                'label' => __('Local Server'),
                'value' => Profile::SAVE_TYPE_LOCAL
            ],
            '1' => [
                'label' => __('Remote FTP'),
                'value' => Profile::SAVE_TYPE_FTP
            ],
            '2' => [
                'label' => __('Email'),
                'value' => Profile::SAVE_TYPE_EMAIL
            ],
        ];
        
        return $this->options;
    }
}
