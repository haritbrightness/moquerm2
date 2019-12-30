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

/**
 * Interface Import
 *
 * @package Aitoc\OrdersExportImport\Api\Data
 */
interface ImportInterface
{
    const IMPORT_ID = 'import_id';
    const STATUS = 'status';
    const NAME = 'filename';
    const CONFIG = 'serialized_config';
    const DATE = 'dt';
    const PROCESSED_COUNT = 'processed_count';
    const IMPORTED_COUNT = 'imported_count';
    const ALL_STATUSES = 'All Statuses';
    
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus();
    
    /**
     * Get Name
     *
     * @return string|null
     */
    public function getFilename();

    /**
     * Get Parameters
     *
     * @return mixed
     */
    public function getConfig();

    /**
     * Get Date
     *
     * @return string
     */
    public function getDate();

    /**
     * Get Processed Count
     *
     * @return boolean|null
     */
    public function getProcessedCount();

    /**
     * Get Imported Count
     *
     * @return string|null
     */
    public function getImportedCount();

    /**
     * Set ID
     *
     * @param $id
     */
    public function setId($id);

    /**
     * Set Status
     *
     * @param $status
     */
    public function setStatus($status);

    /**
     * Set Name
     *
     * @param $name
     */
    public function setFilename($name);

    /**
     * Set Config
     *
     * @param $config
     */
    public function setConfig($config);

    /**
     * Set Date
     *
     * @param $date
     */
    public function setDate($date);

    /**
     * Set Processed Count
     *
     * @param $count
     */
    public function setProcessedCount($count);

    /**
     * Set Imported Count
     *
     * @param $count
     */
    public function setImportedCount($count);
}
