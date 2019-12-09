<?php

namespace Swissup\Orderattachment\Model\ResourceModel\Attachment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Swissup\Orderattachment\Model\Attachment', 'Swissup\Orderattachment\Model\ResourceModel\Attachment');
    }
}
