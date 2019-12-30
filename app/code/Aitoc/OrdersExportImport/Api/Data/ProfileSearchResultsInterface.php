<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ProfileSearchResultsInterface
 *
 * @package Aitoc\OrdersExportImport\Api\Data
 */
interface ProfileSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pages list.
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface[]
     */
    public function getItems();

    /**
     * Set pages list.
     *
     * @param \Aitoc\OrdersExportImport\Api\Data\ProfileInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
