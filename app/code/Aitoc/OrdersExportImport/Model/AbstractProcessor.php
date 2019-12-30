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
 * Class AbstractProcessor
 */
abstract class AbstractProcessor
{
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory
     */
    protected $configFactory;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\ConverterFactory
     */
    protected $converterFactory;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\ContainerFactory
     */
    protected $containerFactory;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory
     */
    protected $entityTypeFactory;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\Config
     */
    protected $config;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\Converter
     */
    protected $converter;
    
    /**
     * @var \Aitoc\OrdersExportImport\Api\ContainerInterface
     */
    protected $container;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\EntityType
     */
    protected $entity;
    
    /**
     * Constructor
     *
     * @param \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory $configFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\ConverterFactory $converterFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\ContainerFactory $containerFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory $configFactory,
        \Aitoc\OrdersExportImport\Model\Processor\ConverterFactory $converterFactory,
        \Aitoc\OrdersExportImport\Model\Processor\ContainerFactory $containerFactory,
        \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory
    ) {
        $this->configFactory     = $configFactory;
        $this->converterFactory  = $converterFactory;
        $this->containerFactory  = $containerFactory;
        $this->entityTypeFactory = $entityTypeFactory;
    }
    
    /**
     * Config Getter
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * For both import and export
     */
    public function initContainer()
    {
        $containers = $this->config->getContainers();

        $this->container = $this->containerFactory->create(
            $containers[$this->config->getFileType()] ?? null
        );
        
        $this->container->open(
            $this->config->getFilename()
        );
    }
    
    /**
     * Init converter
     */
    public function initDataConverter()
    {
        if (!$this->config->getFileType() || !$this->config->getEntity()) {
            throw new \LocalizedException('Converter not configured correctly.');
        }
        
        $this->converter = $this->converterFactory->create();

        $options = $this->config->getData($this->config->getProcessorType());
        $options = $options[$this->config->getEntity()][$this->config->getFileType()] ?? [];
        $filters = $this->config->getDataFilters() ? : [];

        // get options for file type and entity
        foreach ($options as $code => $oData) {
            if (isset($filters[$code])) {
                $this->converter->addFilter($code, $filters[$code]);
            }
        }
        
        $this->converter->addFilterOptions($options);
    }
    
    /**
     * Retrieve entity
     */
    public function getEntity()
    {
        if (!$this->entity) {
            $entities = $this->config->getEntityTypes();
            
            $this->entity = $this->entityTypeFactory->create(
                $entities[$this->config->getEntity()] ?? null,
                ['config' => $this->config]
            );
        }
        return $this->entity;
    }
    
    /**
     * Init and start processing
     *
     * @param object $profile
     */
    public function execute($initConfig)
    {
        if (!is_object($initConfig) || !$initConfig->getData()) {
            throw new \LocalizedException('Please set correct config to processor.');
        }
        
        $this->config = $initConfig;
        
        $this->initContainer();
        $this->initDataConverter();
    }
}
