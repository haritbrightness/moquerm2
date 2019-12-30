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
 * Class ExportConfigMapper
 */
class ExportConfigMapper
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
    public function toConfig($export)
    {
        $profile = $export->getProfile();
        
        $config  = $this->configFactory->create()
            ->setProcessorType(
                'export'
            )->setFilename(
                $export->getFilename()
            )->setFileType(
                $profile->getConfig()->getFileType()
            )->setEntity(
                $profile->getConfig()->getEntity()
            );
        
        return $config;
    }
}
