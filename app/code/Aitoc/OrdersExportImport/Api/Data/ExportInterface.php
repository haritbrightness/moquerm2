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
 * Interface ExportInterface
 *
 * @package Aitoc\OrdersExportImport\Api\Data
 */
interface ExportInterface
{
    const EXPORT_ID         = 'export_id';
    const PROFILE_ID        = 'profile_id';
    const DT                = 'dt';
    const FILENAME          = 'filename';
    const SERIALIZED_CONFIG = 'serialized_config';
    const PROCESSED_COUNT   = 'processed_count';
    const STATUS            = 'status';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getProfileId();

    /**
     * Get DateTime
     *
     * @return string|null
     */
    public function getDt();

    /**
     * Get Filename
     *
     * @return mixed
     */
    public function getFilename();

    /**
     * Get Parameters
     *
     * @return string
     */
    public function getConfig();

    /**
     * Get Orders
     *
     * @return string|null
     */
    public function getProcessedCount();

    /**
     * Get status
     *
     * @return mixed
     */
    public function getStatus();

    /**
     * Set ID
     *
     * @param $id
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setId($id);

    /**
     * Set Profile ID
     *
     * @param $profile
     *
     * @return mixed
     */
    public function setProfileId($profile);

    /**
     * Set Name
     *
     * @param $date
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setDt($date);

    /**
     * Set Filename
     *
     * @param $file
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setFilename($file);

    /**
     * Set Config
     *
     * @param $config
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setConfig($config);

    /**
     * Set Count
     *
     * @param $count
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setProcessedCount($count);

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status);
}
