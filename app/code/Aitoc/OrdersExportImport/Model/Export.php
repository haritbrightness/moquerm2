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

use Aitoc\OrdersExportImport\Api\Data\ExportInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Export
 *
 * @package Aitoc\OrdersExportImport\Model
 */
class Export extends AbstractModel implements ExportInterface
{
    const PROFILE_TYPE = 'export';
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Profile
     */
    protected $profile;
    
    /**
     * @var \DataObject
     */
    protected $serializedConfigObject;
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Aitoc\OrdersExportImport\Model\ProfileFactory $profileFactory,
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Aitoc\OrdersExportImport\Model\ProfileFactory $profileFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->profile = $profileFactory->create();
    }
    
    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Aitoc\OrdersExportImport\Model\ResourceModel\Export::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::EXPORT_ID);
    }

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getProfileId()
    {
        return $this->getData(self::PROFILE_ID);
    }

    /**
     * Get DateTime
     *
     * @return string|null
     */
    public function getDt()
    {
        return $this->getData(self::DT);
    }

    /**
     * Get Filename
     *
     * @return mixed
     */
    public function getFilename()
    {
        return $this->getData(self::FILENAME);
    }

    /**
     * Get Parameters
     *
     * @return mixed
     */
    public function getConfig()
    {
        if (!is_object($this->serializedConfigObject)) {
            $profileConfig = json_decode($this->getData(self::SERIALIZED_CONFIG), true);
            
            $this->serializedConfigObject = new \Magento\Framework\DataObject(
                is_array($profileConfig) ? $profileConfig : []
            );
        }
        
        return $this->serializedConfigObject;
    }
    
    /**
     * Get Count Orders
     *
     * @return string|null
     */
    public function getProcessedCount()
    {
        return $this->getData(self::PROCESSED_COUNT);
    }

    /**
     * Get Status
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set ID
     *
     * @param $id
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setId($id)
    {
        $this->setData(self::EXPORT_ID, $id);
    }

    /**
     * Set Profile ID
     *
     * @param $profile
     *
     * @return mixed
     */
    public function setProfileId($profile)
    {
        $this->setData(self::PROFILE_ID, $profile);
    }

    /**
     * Set Name
     *
     * @param $date
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setDt($date)
    {
        $this->setData(self::DT, $date);
    }

    /**
     * Set Filename
     *
     * @param $file
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setFilename($file)
    {
        $this->setData(self::FILENAME, $file);
    }

    /**
     * Set Config
     *
     * @param $config
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setConfig($config)
    {
        $config = is_array($config) ? $config : [];
        $this->setData(self::SERIALIZED_CONFIG, json_encode($config));
    }

    /**
     * Set Orders Count
     *
     * @param $count
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setProcessedCount($count)
    {
        $this->setData(self::PROCESSED_COUNT, $count);
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
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
     * @param null $date
     * @return mixed
     */
    public function getProfile()
    {
        if (!$this->profile->getId()) {
            $this->profile->load($this->getProfileId());
        }

        return $this->profile;
    }
}
