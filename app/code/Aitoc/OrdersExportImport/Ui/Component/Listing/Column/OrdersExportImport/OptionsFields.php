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
use Magento\Framework\Escaper;
use Magento\Store\Model\System\Store as SystemStore;

/**
 * Store Options for Cms Pages and Blocks
 */
class OptionsFields extends StoreOptions
{
    /**
     * @var \Aitoc\OrdersExportImport\Helper\Helper
     */
    private $helper;

    /**
     * OptionsFields constructor.
     *
     * @param SystemStore                             $systemStore
     * @param Escaper                                 $escaper
     * @param \Aitoc\OrdersExportImport\Helper\Helper $helper
     */
    public function __construct(
        SystemStore $systemStore,
        Escaper $escaper,
        \Aitoc\OrdersExportImport\Helper\Helper $helper
    ) {
        parent::__construct($systemStore, $escaper);
        $this->helper = $helper;
    }

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
        $fields = $this->helper->getFields('sales_order');
        foreach ($fields as $value) {
            $options[] = ['label' => $value, 'value' => $value];
        }
        $this->options = $options;

        return $this->options;
    }
}
