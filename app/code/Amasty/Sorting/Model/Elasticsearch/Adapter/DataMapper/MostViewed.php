<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Amasty\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;
use Amasty\Sorting\Model\ResourceModel\Method\MostViewed as MostViewedResource;
use Amasty\Sorting\Helper\Data;
use Magento\Store\Model\ScopeInterface;

/**
 * Class MostViewed
 */
class MostViewed extends IndexedDataMapper
{
    const FIELD_NAME = 'most_viewed';

    public function __construct(
        MostViewedResource $resourceMethod,
        Data $helper
    ) {
        parent::__construct($resourceMethod, $helper);
    }
}
