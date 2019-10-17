<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Amasty\Sorting\Model\ResourceModel\Method\Wished as WishedResource;
use Amasty\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;
use Amasty\Sorting\Helper\Data;

/**
 * Class Wished
 */
class Wished extends IndexedDataMapper
{
    const FIELD_NAME = 'wished';

    public function __construct(
        WishedResource $resourceMethod,
        Data $helper
    ) {
        parent::__construct($resourceMethod, $helper);
    }
}
