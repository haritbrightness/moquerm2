<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\Widgets;

use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Model\ResourceModel\Tag\Collection as TagCollection;
use Amasty\Faq\Block\Lists\TagList;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\DataObject\IdentityInterface;

class TagsBlock extends Template implements BlockInterface, IdentityInterface
{
    const DEFAULT_TAGS_LIMIT = 20;

    /**
     * @var string
     */
    protected $_template = 'Amasty_Faq::tags_block.phtml';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var TagCollection
     */
    private $tagCollection;

    /**
     * @var TagList
     */
    private $tagList;

    /**
     * TagsBlock constructor.
     * @param ConfigProvider $configProvider
     * @param Template\Context $context
     * @param TagCollection $tagCollection
     * @param TagList $tagList
     * @param array $data
     */
    public function __construct(
        ConfigProvider $configProvider,
        Template\Context $context,
        TagCollection $tagCollection,
        TagList $tagList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->tagCollection = $tagCollection;
        $this->tagList = $tagList;
        $this->setData('cache_lifetime', 86400);
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        if (!$this->configProvider->isEnabled()) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return string
     */
    public function getTagsLimit()
    {
        if (!$this->hasData('tags_limit')) {
            $this->setData('tags_limit', self::DEFAULT_TAGS_LIMIT);
        }

        return $this->getData('tags_limit');
    }

    /**
     * @return array
     */
    public function getTags()
    {
        $preparedTags = [];
        $storeId = $this->_storeManager->getStore()->getId();
        $tags = $this->tagCollection->getTagsSortedByCount($this->getTagsLimit(), $storeId);
        foreach ($tags as $tag) {
            $preparedTags[] = [
                'title' => $tag->getTitle(),
                'count' => $tag->getCount(),
                'link' => $this->tagList->getTagLink($tag->getTitle())
            ];
        }

        return $preparedTags;
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [
            TagCollection::CACHE_TAG,
            \Amasty\Faq\Model\ResourceModel\Question\Collection::CACHE_TAG
        ];
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return parent::getCacheKeyInfo() + ['tag_limit' => $this->getTagsLimit()];
    }
}
