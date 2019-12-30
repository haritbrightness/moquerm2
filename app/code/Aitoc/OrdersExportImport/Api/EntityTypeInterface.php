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
 * Interface EntityTypeInterface
 */
interface EntityTypeInterface
{
    /**
     * Validate data before saving
     *
     * @param array $data
     */
    public function validate($data);
    
    /**
     * Save data to database
     *
     * @param array $data
     */
    public function save($data);
}
