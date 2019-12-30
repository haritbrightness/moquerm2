<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Processor\Config;

/**
 * Class ImportConfigMapper
 */
class ImportConfigMapper
{
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory
     */
    protected $configFactory;
    
    /**
     * Constructor
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory $configFactory
    ) {
        $this->configFactory = $configFactory;
    }
    
    /**
     *
     */
    public function toConfig($import)
    {
        $config = $this->configFactory->create()
            ->setProcessorType(
                'import'
            )->setFileType(
                $import->getConfig()->getFileType()
            )->setFilename(
                $import->getFilename()
            )->setEntity(
                $import->getConfig()->getEntity()
            )->setImportBehavior(
                $import->getConfig()->getImportBehavior()
            )->setProfileResult(
                $import->getConfig()->getProfileResult()
            )->setStoreId(
                $import->getConfig()->getStoreId()
            )->setCustomersAutocreate(
                $import->getConfig()->getCustomersAutocreate()
            )->setTryStoreviews(
                $import->getConfig()->getTryStoreviews()
            );
        
        return $config;
    }
}
