<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Api;

/**
 * Interface DataFilterInterface
 */
interface DataFilterInterface
{
    /**
     * Transforms data by rules
     *
     * @param array $data
     * @return mixed
     */
    public function execute($data, &$out);
}
