<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Processor\EntityType;

use Magento\Store\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;

/**
 * Class CreditMemo
 */
class Creditmemo extends \Aitoc\OrdersExportImport\Model\Processor\EntityType\AbstractEntity
{
    /**
     * Entity Type unique code
     */
    const ENTITY_CODE = 'creditmemo';
    
    /**
     * Additional data items
     */
    const CREDITMEMO_ITEM    = 'item';
    const CREDITMEMO_COMMENT = 'comment';
    
    /**
     * Megento entity class to be imported
     */
    protected $model = \Magento\Sales\Model\Order\Creditmemo::class;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\ItemFactory
     */
    protected $itemFactory;
    
    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\CommentFactory
     */
    protected $commentFactory;
    
    /**
     * Constructor
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory,
        \Aitoc\OrdersExportImport\Model\Processor\Config $config,
        StoreCollectionFactory $storeCollectionFactory,
        \Magento\Sales\Model\Order\Creditmemo\ItemFactory $itemFactory,
        \Magento\Sales\Model\Order\Creditmemo\CommentFactory $commentFactory
    ) {
        parent::__construct($objectManager, $storeCollectionFactory, $entityTypeFactory, $config);
        $this->itemFactory      = $itemFactory;
        $this->commentFactory   = $commentFactory;
    }

    /**
     * Get extras
     *
     * @return array
     */
    protected function prepareExtras()
    {
        // Magento cache sub-collections itself
        // so repeated calls performs much faster
        $extra = [
            self::CREDITMEMO_ITEM    => $this->getEntity()->getItemsCollection(),
            self::CREDITMEMO_COMMENT => $this->getEntity()->getCommentsCollection(),
        ];

        return $extra;
    }
    
    /**
     * Validate data of current entity
     * throw exception on validation fails
     *
     * @param array $data
     */
    protected function validateEntity($data)
    {
        $this->validateIncrement($data['fields'] ?? []);
        
        if (empty(['item'])) {
            throw new \Exception(__('Credit memo items are missing'));
        }
    }
    
    /**
     * @param array $entityData
     */
    private function validateIncrement($entityData)
    {
        if (empty($entityData['increment_id'])) {
            throw new \Exception(
                __("Credit memo have not Increment Id")
            );
        } elseif ($this->config->getImportBehavior() == 'append') {
            $object = $this->initEntity();
            $object->load($entityData['increment_id'], 'increment_id');
            
            if ($object->getId()) {
                throw new \Exception(__('Credit memo #%1 already exist.', $object->getIncrementId()));
            }
        }
    }

    /**
     * @param array $extraData
     */
    public function saveEntity($extraData)
    {
        $store = $this->correctStore($this->modelEntity->getStoreId());
        $this->modelEntity
            ->setEntityId(null)
            ->setOrderId($this->getParent()->getEntity()->getId())
            ->setStoreId($store['store_id']);
        
        parent::saveEntity($extraData);
        
        $this->addItems($extraData['item']);
        $this->addComments($extraData['comment'] ?? []);
    }

    /**
     * @param array $items
     */
    private function addItems($items)
    {
        $orderItems = $this->getOrderItemIds();
        
        foreach ($items as $item) {
            $item['entity_id']     = null;
            $item['product_id']    = null;
            // link Creditmemo item with order item by name
            $item['order_item_id'] = $orderItems[$item['order_item_id']] ?? null;
            $item['quote_item_id'] = null;
            
            $store = $this->correctStore($item['store_id'] ?? null);
            $item['store_id'] = $store['store_id'];
            
            $itemModel = $this->itemFactory->create();
            
            $itemModel->setData($item)
                ->setCreditmemo($this->modelEntity)
                ->setParentId($this->modelEntity->getId())
                ->save();
        }
    }
    
    /**
     * Returns name to id hash list
     *
     * @return array
     */
    private function getOrderItemIds()
    {
        $list = [];
        $items = $this->getParent()->getEntity()->getItems();
        if (count($items)) {
            foreach ($items as $item) {
                $list[$item->getOldItemId()] = $item->getId();
            }
        }
        return $list;
    }

    /**
     * @param array $comments
     */
    private function addComments($comments)
    {
        foreach ($comments as $item) {
            $item['entity_id'] = null;
            $item['parent_id'] = null;

            $store = $this->correctStore($item['store_id'] ?? null);
            $item['store_id'] = $store['store_id'];
            
            $comment = $this->commentFactory->create();
            
            $comment->setData($item)
                ->setCreditmemo($this->modelEntity)
                ->setParentId($this->modelEntity->getId())
                ->save();
        }
    }
}
