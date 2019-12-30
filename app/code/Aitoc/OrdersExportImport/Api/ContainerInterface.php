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
 * Interface ContainerInterface
 */
interface ContainerInterface
{

    /**
     * Returns next record from source
     *
     * @return array
     */
    public function next();
    
    /**
     * Returns current record 'as is' from source
     *
     * @return string
     */
    public function current();
}
