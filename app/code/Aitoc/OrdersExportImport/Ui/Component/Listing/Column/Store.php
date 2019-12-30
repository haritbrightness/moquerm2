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
namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Column;

/**
 * Class Store
 *
 * @package Aitoc\OrdersExportImport\Ui\Component\Listing\Column
 */
class Store extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    /**
     * @param array $item
     *
     * @return \Magento\Framework\Phrase
     */
    protected function prepareItem(array $item)
    {
        if (empty($item[$this->storeKey])) {
            return __('All Store Views');
        }

        parent::prepareItem($item);
    }
}
