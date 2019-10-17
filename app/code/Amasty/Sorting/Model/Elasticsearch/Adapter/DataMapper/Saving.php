<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Amasty\Sorting\Helper\Data;
use Amasty\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;
use Amasty\Sorting\Model\ResourceModel\Method\Saving as SavingResource;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class Saving
 */
class Saving extends IndexedDataMapper
{
    const FIELD_NAME = 'saving';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        SavingResource $resourceMethod,
        Data $helper
    ) {
        parent::__construct($resourceMethod, $helper);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    protected function forceLoad($storeId)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addPriceData();
        $this->resourceMethod->setLimitColumns(true);
        $this->resourceMethod->apply($collection, '');
        return $this->resourceMethod->getConnection()->fetchPairs($collection->getSelect());
    }
}
