<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Amasty\Sorting\Model\ResourceModel\Method\Commented as CommentedResource;
use Amasty\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;
use Amasty\Sorting\Helper\Data;

/**
 * Class Commented
 */
class Commented extends IndexedDataMapper
{
    const FIELD_NAME = 'reviews_count';

    public function __construct(
        CommentedResource $resourceMethod,
        Data $helper
    ) {
        parent::__construct($resourceMethod, $helper);
    }
}
