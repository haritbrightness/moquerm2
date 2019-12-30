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

use Aitoc\OrdersExportImport\Api\Data\ProfileInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Filesystem\DriverPool;

/**
 * Class Profile
 */
class Profile extends AbstractModel implements ProfileInterface
{
    /**
     * Export save type
     */
    const SAVE_TYPE_LOCAL = 'local';
    const SAVE_TYPE_FTP   = 'ftp';
    const SAVE_TYPE_EMAIL = 'email';
    
    /**
     * Statuses
     */
    const STATUS_QUEUE      = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETE   = 2;
    
    /**
     * Types
     */
    const TYPE_MANUAL   = 'manual';
    const TYPE_CHECKOUT = 'checkout';
    const TYPE_INVOICE  = 'invoice';
    
    /**
     * @var \DataObject
     */
    protected $serializedConfigObject;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\ExportFactory
     */
    protected $exportFactory;
    
    /**
     * Directory Handle
     */
    protected $directoryHandle;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\ResourceModel\Profile\CollectionFactory
     */
    protected $exportCollectionFactory;
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface[]
     */
    protected $writeFactory;
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Aitoc\OrdersExportImport\Model\ProfileFactory $profileFactory,
     * @param \Aitoc\OrdersExportImport\Model\ExportFactory $exportFactory,
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Aitoc\OrdersExportImport\Model\ExportFactory $exportFactory,
        \Aitoc\OrdersExportImport\Model\ResourceModel\Export\CollectionFactory $exportCollectionFactory,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->exportFactory            = $exportFactory;
        $this->exportCollectionFactory  = $exportCollectionFactory;
        $this->writeFactory             = $writeFactory;
    }
    
    protected function _construct()
    {
        $this->_init(\Aitoc\OrdersExportImport\Model\ResourceModel\Profile::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::PROFILE_ID);
    }

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
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
     * Set ID
     *
     * @param $id
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setId($id)
    {
        $this->setData(self::PROFILE_ID, $id);
    }

    /**
     * Set Store ID
     *
     * @param $store
     *
     * @return mixed
     */
    public function setStoreId($store)
    {
        $this->setData(self::STORE_ID, $store);
    }

    /**
     * Set Name
     *
     * @param $name
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * Set Config
     *
     * @param $config
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
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
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setDate($date)
    {
        $this->setData(self::DATE, $date);
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
     * @return string
     */
    public function getFileExtention()
    {
        switch ($this->getConfig()->getFileType()) {
            case 'xml':
                $ext = '.xml';
                break;
                
            case 'csv':
            case 'csvmultiline':
                $ext = '.csv';
                break;
        }
        
        return $ext;
    }
    
    /**
     * @return Export
     */
    public function createTask()
    {
        $config = $this->getConfig();
        $prefix = $config->getPrefix() ? : 'order_export';
        
        $file = $prefix . '_'  . $this->getId() . date('YmdHis') . $this->getFileExtention();
        $path = $config->getPathLocal();
        
        if (substr($path, 0, 1) !== '/') {
            $path = BP . '/' . $path;
        }
        $write = $this->writeFactory->create($path, DriverPool::FILE);
        $write->touch($file);
        
        $export = $this->exportFactory->create();
        $export->setProfileId($this->getId());
        $export->setDt(date('Y-m-d H:i:s'));
        $export->setIsCron(
            $config->getExportType() < 3 ? false : true
        );
        $export->setFileName($path . $file);

        return $export;
    }
    
    /**
     * @return Export
     */
    public function getActiveExportCollection()
    {
        $exportCollection = $this->exportCollectionFactory->create();
        $exportCollection->addFieldToFilter('profile_id', ['eq' => $this->getId()]);
        $exportCollection->addFieldToFilter('status', [
            'in' => [Profile::STATUS_QUEUE, Profile::STATUS_PROCESSING]
        ]);
        
        return $exportCollection;
    }
    
    /**
     * @return Export
     */
    public function updateTasksWithData($newIds)
    {
        $newIds  = is_array($newIds) ? $newIds : [$newIds];
        $exports = $this->getActiveExportCollection();
        
        // append new data to existing active task
        if ($exports->count()) {
            foreach ($exports as $export) {
                $existingIds = $export->getConfig()->getSelected() ?? [];
                // tag 'selected' not empty
                // 'selected is empty when user presses 'Select All' and thre is no need to add new id
                if (count($existingIds)) {
                    $existingIds = array_merge($existingIds, $newIds);
                    $export->getConfig()->setSelected($existingIds);
                    $export->save();
                }
            }
        // create new task for this profile
        } else {
            $export = $this->createTask();
            $export->getConfig()->setSelected($newIds);
            $export->save();
        }
    }
}
