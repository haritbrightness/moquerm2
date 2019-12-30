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

use Aitoc\OrdersExportImport\Model\Import;

/**
 * Class ImportProcessor
 */
class ImportProcessor extends AbstractProcessor
{
    /**
     * Import behavior Types
     */
    const IMPORT_BEHAVIOR_APPEND  = 'append';
    const IMPORT_BEHAVIOR_REPLACE = 'replace';
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\Config\Config\ImportConfigMapperFactory
     */
    protected $configMapperFactory;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\ResultProcessorFactory
     */
    protected $resultProcessorFactory;
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\Import
     */
    protected $profile;

    /**
     * Constructor
     *
     * @param \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory $configFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\ConverterFactory $converterFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\ContainerFactory $containerFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory
     * @param \Aitoc\OrdersExportImport\Model\Processor\Config\ImportConfigMapperFactory $configMapperFactory
     * @param \Aitoc\OrdersExportImport\Model\ResultProcessorFactory $resultProcessorFactory
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\Processor\ConfigFactory $configFactory,
        \Aitoc\OrdersExportImport\Model\Processor\ConverterFactory $converterFactory,
        \Aitoc\OrdersExportImport\Model\Processor\ContainerFactory $containerFactory,
        \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory,
        \Aitoc\OrdersExportImport\Model\Processor\Config\ImportConfigMapperFactory $configMapperFactory,
        \Aitoc\OrdersExportImport\Model\ResultProcessorFactory $resultProcessorFactory
    ) {
        parent::__construct(
            $configFactory,
            $converterFactory,
            $containerFactory,
            $entityTypeFactory
        );
        
        $this->configMapperFactory = $configMapperFactory;
        $this->resultProcessorFactory = $resultProcessorFactory;
    }

    /**
     * Init and start processing
     *
     * @param $import
     * @return array
     */
    public function execute($import)
    {
        $this->profile = $import;
        
        $mapper = $this->configMapperFactory->create();
        parent::execute($mapper->toConfig($import));
        
        $this->initResultAggregator();
        $errors = $this->import();
        $this->updateProgress();
        return $errors;
    }

    /**
     * @return array
     */
    public function import()
    {
        $this->container->rewind((int)$this->profile->getProcessedCount());
        $errors = [];
        for ($n = 0; $n < $this->config->getBulkCount(); $n++) {
            try {
                $record = $this->container->next();

                // done
                if ($record === false) {
                    $this->finalizeImport();
                    break;
                }

                $record = $this->converter->apply($record);
                $this->getEntity()->validate($record);
                $this->getEntity()->save($record);

                $this->profile->incImportedCount(1);
            // log errors
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
                $this->resultProcessor->append($this->container->currentPlain(), $e->getMessage());
            }
        }

        return $errors;
    }
    
    /**
     * Init output import data aggregator
     */
    public function initResultAggregator()
    {
        $this->resultProcessor = $this->resultProcessorFactory->create();
        $this->resultProcessor->execute($this->profile);
    }
    
    /**
     * Update after process is finished
     */
    public function finalizeImport()
    {
        $resultFile = $this->resultProcessor->finalize();
        if ($resultFile) {
            $this->profile->getConfig()->setProfileResult($resultFile);
        }
        
        $this->profile->setStatus(Import::STATUS_COMPLETE);
    }
    
    /**
     * Update profile after each iteration
     */
    public function updateProgress()
    {
        $this->profile->setProcessedCount($this->container->getPosition());
        if ($this->profile->getStatus() != Import::STATUS_COMPLETE) {
            $this->profile->setStatus(Import::STATUS_PROCESSING);
        }
        
        $this->profile->save();
    }
}
