<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model;

use Magento\Framework\Model\AbstractModel;
use Aitoc\OrdersExportImport\Api\Data\ImportInterface;

/**
 * Class Import
 *
 * @package Aitoc\OrdersExportImport\Model
 */
class Import extends AbstractModel implements ImportInterface
{
    /**
     * Profile type
     */
    const PROFILE_TYPE = 'import';
    
    /**
     * Import statuses
     */
    const STATUS_QUEUE      = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETE   = 2;
    
    /**
     * @var \DataObject
     */
    protected $serializedConfigObject;
    
    /**
     * Cosntructor
     */
    protected function _construct()
    {
        $this->_init(\Aitoc\OrdersExportImport\Model\ResourceModel\Import::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::IMPORT_ID);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getFilename()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }
    
    /**
     * Get Parameters
     *
     * @return mixed
     */
    public function getConfig()
    {
        if (!is_object($this->serializedConfigObject)) {
            $profileConfig = json_decode($this->getData(self::CONFIG), true);
            
            $this->serializedConfigObject = new \Magento\Framework\DataObject(
                is_array($profileConfig) ? $profileConfig : []
            );
        }
        
        return $this->serializedConfigObject;
    }

    /**
     * Get Date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * Get Processed Count
     *
     * @return boolean|null
     */
    public function getProcessedCount()
    {
        return $this->getData(self::PROCESSED_COUNT);
    }

    /**
     * Get Imported Count
     *
     * @return string|null
     */
    public function getImportedCount()
    {
        return $this->getData(self::IMPORTED_COUNT);
    }

    /**
     * Set ID
     *
     * @param $id
     */
    public function setId($id)
    {
        $this->setData(self::IMPORT_ID, $id);
    }

    /**
     * Set Name
     *
     * @param $name
     */
    public function setFilename($name)
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * Set Status
     *
     * @param $status
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * Set Config
     *
     * @param $config
     */
    public function setConfig($config)
    {
        $config = is_array($config) ? $config : [];
        $this->setData(self::CONFIG, json_encode($config));
    }

    /**
     * Set Date
     *
     * @param $date
     */
    public function setDate($date)
    {
        $this->setData(self::DATE, $date);
    }

    /**
     * Set Processed Count
     *
     * @param $count
     */
    public function setProcessedCount($count)
    {
        $this->setData(self::PROCESSED_COUNT, $count);
    }

    /**
     * Set Imported Count
     *
     * @param $count
     */
    public function setImportedCount($count)
    {
        $this->setData(self::IMPORTED_COUNT, $count);
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    public function beforeSave()
    {
        $this->setConfig($this->getConfig()->getData());
        
        return parent::beforeSave();
    }
    
    /**
     * Add Imported Count
     *
     * @param $increment
     */
    public function incImportedCount($increment = 1)
    {
        $this->setData(
            self::IMPORTED_COUNT,
            $this->getData(self::IMPORTED_COUNT) + $increment
        );
    }
    
    /**
     * Collection sum of result files size
     *
     * @return int
     */
    public function collectTotalFilesize()
    {
        $collection = $this->getCollection();
        
        $totalSize = 0;
        
        foreach ($collection as $import) {
            $file = $import->getConfig()->getProfileResult();
            if (is_file($file)) {
                $totalSize += filesize($file);
            }
        }
        
        return $totalSize;
    }
}
