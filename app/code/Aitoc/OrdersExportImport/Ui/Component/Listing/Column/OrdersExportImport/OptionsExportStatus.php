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
 * OptionsExportStatus
 */
class OptionsExportStatus extends StoreOptions
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
                'label' => __('In queue'),
                'value' => Profile::STATUS_QUEUE
            ],
            [
                'label' => __('Processing'),
                'value' => Profile::STATUS_PROCESSING
            ],
            [
                'label' => __('Complete'),
                'value' => Profile::STATUS_COMPLETE
            ],
        ];
        
        $this->options = $options;

        return $this->options;
    }
}
