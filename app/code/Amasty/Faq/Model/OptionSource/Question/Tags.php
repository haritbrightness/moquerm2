<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\OptionSource\Question;

use Magento\Framework\Option\ArrayInterface;
use Amasty\Faq\Model\ResourceModel\Tag\CollectionFactory;
use Amasty\Faq\Api\Data\TagInterface;

class Tags implements ArrayInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * Tags constructor.
     *
     * @param CollectionFactory $collection
     */
    public function __construct(
        CollectionFactory $collection
    ) {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->collection->create();
        $tags = $collection->getData();
        $result = [];
        foreach ($tags as $tag) {
            $result[] = ['value' => $tag[TagInterface::TAG_ID], 'label' => $tag[TagInterface::TITLE]];
        }

        return $result;
    }
}
