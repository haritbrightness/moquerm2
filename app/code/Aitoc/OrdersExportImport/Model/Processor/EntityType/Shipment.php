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
use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;

/**
 * Class Shipment
 */
class Shipment extends \Aitoc\OrdersExportImport\Model\Processor\EntityType\AbstractEntity
{
    /**
     * Entity Type unique code
     */
    const ENTITY_CODE = 'shipment';
    
    /**
     * Additional data items
     */
    const SHIPMENT_ITEM     = 'item';
    const SHIPMENT_COMMENT  = 'comment';
    const SHIPMENT_TRACKING = 'trackinginformation';
    
    /**
     * Magento entity class to be imported
     */
    protected $model = \Magento\Sales\Model\Order\Shipment::class;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\ItemFactory
     */
    protected $itemFactory;
    
    /**
     * @var \Magento\Sales\Model\Order\Shipment\CommentFactory
     */
    protected $commentFactory;
    
    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    protected $trackFactory;

    /**
     * @var StockItemInterfaceFactory
     */
    protected $stockItemInterfaceFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Constructor
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory,
        \Aitoc\OrdersExportImport\Model\Processor\Config $config,
        StoreCollectionFactory $storeCollectionFactory,
        \Magento\Sales\Model\Order\Shipment\ItemFactory $itemFactory,
        \Magento\Sales\Model\Order\Shipment\CommentFactory $commentFactory,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        StockItemInterfaceFactory $stockItemInterfaceFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($objectManager, $storeCollectionFactory, $entityTypeFactory, $config);
        $this->itemFactory    = $itemFactory;
        $this->commentFactory = $commentFactory;
        $this->trackFactory   = $trackFactory;
        $this->stockItemInterfaceFactory   = $stockItemInterfaceFactory;
        $this->productMetadata   = $productMetadata;
    }

    /**
     * Get extras
     *
     * @return array
     */
    protected function prepareExtras()
    {
        //get actual items or just collection class
        $shipmentCollection = $this->getEntity()->getId()
            ? $this->getEntity()->getItemsCollection()
            : $this->itemFactory->create();
        // Magento cache sub-collections itself
        // so repeate calls performs much faster
        $extra = [
            self::SHIPMENT_ITEM     => $shipmentCollection,
            self::SHIPMENT_COMMENT  => $this->getEntity()->getCommentsCollection(),
            self::SHIPMENT_TRACKING => $this->getEntity()->getTracksCollection(),
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

        if (empty($data['item'])) {
            throw new \Exception(__('Shipment items are missing'));
        }
    }
    
    /**
     * @param array $entityData
     */
    private function validateIncrement($entityData)
    {
        if (empty($entityData['increment_id'])) {
            throw new \Exception(
                __("Shipment have not Increment Id")
            );
        } elseif ($this->config->getImportBehavior() == 'append') {
            $object = $this->initEntity();
            $object->load($entityData['increment_id'], 'increment_id');
            
            if ($object->getId()) {
                throw new \Exception(__('Shipment #%1 already exist.', $object->getIncrementId()));
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
        
        $this->modelEntity->setPackages(null);
        
        $this->addItems($extraData['item']);

        $this->processCompatabilityWithMsi();
        $this->modelEntity->setIsAitocImported(true);
        parent::saveEntity($extraData);
        
        $this->addComments($extraData['comment'] ?? []);
        $this->addTrackingInfo($extraData['trackinginformation'] ?? []);
    }

    /**
     * @return bool
     */
    private function processCompatabilityWithMsi()
    {
        $result = [];
        $shipmentItems = $this->modelEntity->getItems();

        if ($shipmentItems) {
            foreach ($shipmentItems as $key => $item) {
                $item->setIsAitocImported(true);
                $result[$key] = $item;
            }

            $this->modelEntity->setItems($result);

            return true;
        }

        return false;
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
                ->setShipment($this->modelEntity)
                ->setParentId($this->modelEntity->getId());

            $this->modelEntity->addItem($itemModel);
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
     * @return array
     */
    private function getTrackingInformationData()
    {
        $data = [];
        
        foreach ($this->modelEntity->getAllTracks() as $item) {
            $data[] = $item->getData();
        }
        
        return $data;
    }
    
    /**
     * @return array
     */
    private function addComments($comments)
    {
        foreach ($comments as $item) {
            $item['entity_id'] = null;

            $store = $this->correctStore($item['store_id'] ?? null);
            $item['store_id'] = $store['store_id'];
            
            $comment = $this->commentFactory->create();
            
            $comment->setData($item)
                ->setShipment($this->modelEntity)
                ->setParentId($this->modelEntity->getId())
                ->save();
        }
    }
    
    /**
     * @return array
     */
    private function addTrackingInfo($trackings)
    {
        foreach ($trackings as $item) {
            $item['entity_id'] = null;
            $item['order_id']  = $this->getParent()->getEntity()->getId();
            
            $track = $this->trackFactory->create();
            
            $track->setData($item)
                ->setShipment($this->modelEntity)
                ->setParentId($this->modelEntity->getId())
                ->save();
        }
    }
}
