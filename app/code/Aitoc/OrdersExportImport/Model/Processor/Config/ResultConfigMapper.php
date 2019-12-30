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
 * Class ResultConfigMapper
 */
class ResultConfigMapper
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
                'result'
            )->setFilename(
                $this->sourceNameToDestination($import->getFilename())
            )->setFileType(
                $import->getConfig()->getFileType()
            )->setEntity(
                $import->getConfig()->getEntity()
            );
        
        return $config;
    }
    
    /**
     * Convert filename
     */
    protected function sourceNameToDestination($source)
    {
        return preg_replace('/(\.[^.]+)$/', sprintf('%s$1', '_errors'), $source);
    }

}
