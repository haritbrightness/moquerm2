<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Amasty\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;
use Amasty\Sorting\Model\ResourceModel\Method\Bestselling;
use Amasty\Sorting\Helper\Data;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Bestseller
 */
class Bestseller extends IndexedDataMapper
{
    const FIELD_NAME = 'bestsellers';

    public function __construct(
        Bestselling $resourceMethod,
        Data $helper
    ) {
        parent::__construct($resourceMethod, $helper);
    }
}
