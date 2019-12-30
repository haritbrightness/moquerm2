<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Cron;

/**
 * Class Import
 */
class Import
{
    /**
     * @var \Aitoc\OrdersExportImport\Model\ImportProcessorFactory
     */
    private $processorFactory;

    /**
     * @var \Aitoc\OrdersExportImport\Model\ResourceModel\Import\CollectionFactory
     */
    private $collectionFactory;
    
    /**
     * Constructor
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\ImportProcessorFactory $processorFactory,
        \Aitoc\OrdersExportImport\Model\ResourceModel\Import\CollectionFactory $collectionFactory
    ) {
        $this->processorFactory  = $processorFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute import cron event
     *
     * @return void
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $collection->readyForImportFilter();
        
        if ($collection->count()) {
            $processor = $this->processorFactory->create();
            $processor->execute($collection->getFirstItem());
        }
    }
}
