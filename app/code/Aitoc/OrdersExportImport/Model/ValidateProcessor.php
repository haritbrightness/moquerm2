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

/**
 * Class ValidateProcessor
 */
class ValidateProcessor extends AbstractProcessor
{
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\ImportConfigMapperFactory
     */
    protected $configMapperFactory;
    
    /**
     * Constructor
     *
     * @param \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory $configFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\ConverterFactory $converterFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\ContainerFactory $containerFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\Config\ImportConfigMapperFactory $configMapperFactory
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory $configFactory,
        \Aitoc\OrdersExportImport\Model\Processor\ConverterFactory $converterFactory,
        \Aitoc\OrdersExportImport\Model\Processor\ContainerFactory $containerFactory,
        \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory,
        \Aitoc\OrdersExportImport\Model\Processor\Config\ImportConfigMapperFactory $configMapperFactory
    ) {
        parent::__construct(
            $configFactory,
            $converterFactory,
            $containerFactory,
            $entityTypeFactory
        );
        
        $this->configMapperFactory = $configMapperFactory;
    }
    
    /**
     * Init and start processing
     *
     * @param $profile
     */
    public function execute($profile)
    {
        $mapper = $this->configMapperFactory->create();
        parent::execute($mapper->toConfig($profile));
        
        $this->validate();
    }
    
    /**
     * Validate data
     */
    public function validate()
    {
        $record = $this->container->next();
        
        if ($record === false) {
            throw new \Exception('No records found in the file.');
        }
        
        $record = $this->converter->apply($record);

        // validate first record in file
        $this->getEntity()->validate($record);
    }
}
