<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Amasty\Sorting\Model\ResourceModel\Method\Toprated as TopratedResource;
use Amasty\Sorting\Helper\Data;
use Amasty\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;

/**
 * Class Toprated
 */
class Toprated extends IndexedDataMapper
{
    const FIELD_NAME = 'rating_summary_field';

    public function __construct(
        TopratedResource $resourceMethod,
        Data $helper
    ) {
        parent::__construct($resourceMethod, $helper);
    }
}
