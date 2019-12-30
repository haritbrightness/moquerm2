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

use Aitoc\OrdersExportImport\Api\EntityTypeInterface;
use Magento\Store\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
/**
 * Class AbstractEntity
 */
class AbstractEntity implements EntityTypeInterface
{
    /*
     * Entity data container
     */
    const FIELDS = 'fields';
    
    /**
     * Child action types
     */
    const CHILD_ACTION_VALIDATE = 'validate';
    const CHILD_ACTION_SAVE     = 'save';
    const CHILD_ACTION_RETRIEVE = 'retrieve';
    
    /**
     * Entity Type unique code
     */
    const ENTITY_CODE = '';
    
    /**
     * Megento entity class to be imported
     */
    protected $model;
    
    /**
     * Array of sub-entities classes
     */
    protected $children = [];
    
    /**
     * Entity
     */
    protected $modelEntity;
    
    /**
     * Entity
     */
    protected $childEntities = [];
    
    /**
     * Parent Entity
     */
    protected $parentEntity = null;
    
    /**
     * @var StoreCollectionFactory
     */
    protected $storeCollectionFactory;
    
    /**
     * Config
     */
    protected $config;
    
    /**
     * Entity fields
     */
    //protected $fields = [];
    protected $scheme;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory
     */
    protected $entityTypeFactory;
    
    /**
     * Constructor
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        StoreCollectionFactory $storeCollectionFactory,
        \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory,
        \Aitoc\OrdersExportImport\Model\Processor\Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->entityTypeFactory = $entityTypeFactory;
        $this->config = $config;
        
        $this->init();
    }

    /**
     *
     * @param
     * @return bool
     */
    public function init()
    {
        // should be $this to check end-model const
        if (empty($this::ENTITY_CODE)) {
            throw new \Exception('Entity type code is empty.');
        }
        
        foreach ($this->children as $childEntity) {
            $this->childEntities[$childEntity::ENTITY_CODE] = $this->entityTypeFactory->create(
                $childEntity,
                ['config' => $this->config]
            );
        }
    }
    
    /**
     * Get Parent Entity
     */
    public function getParent()
    {
        return $this->parentEntity;
    }

    /**
     * Set Parent Entity
     */
    public function setParent($entity)
    {
        $this->parentEntity = $entity;
    }
    
    /**
     * List Ids
     */
    public function listByCondition($filterParams, $pageSize = null)
    {
        $collection = $this->initEntity()->getCollection();
        
        foreach ($filterParams as $filter) {
            foreach ($filter as $field => $condition) {
                $collection->addFieldToFilter($field, $condition);
            }
        }
        
        if ($pageSize) {
            $collection->setPageSize($pageSize)->setCurPage(1);
        }
        
        return $collection;
    }
    
    /**
     * Prepare data fields
     */
    public function prepareFields($collection, $fieldList = [])
    {
        if (count($fieldList)) {
            $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
            $collection->getSelect()->columns($fieldList);
        }
        return $collection;
    }

    /**
     * Get Entity Fields
     *
     * @return array
     */
    public function getEntityFields($entity = null)
    {
        if ($entity == null) {
            $entity = $this->getEntity();
        }
        
        return array_keys(
            $entity->getResource()->getConnection()->describeTable(
                $entity->getResource()->getMainTable()
            )
        );
    }

    /**
     * Get data structure
     *
     * @return array
     */
    public function getDataScheme()
    {
        if ($this->scheme != null) {
            $scheme = $this->scheme;
        } else {
            $scheme[self::FIELDS] = $this->getEntityFields();
            
            foreach ($this->prepareExtras() as $index => $source) {
                $scheme[$index] = $this->getEntityFields($source);
            }
        }
        
        foreach ($this->childEntities as $childEntity) {
            $scheme[$childEntity::ENTITY_CODE] = $childEntity->getDataScheme();
        }
        
        return $scheme;
    }

    /**
     * Set data structure
     *
     * @param array $scheme
     */
    public function setDataScheme($scheme)
    {
        //  recreate scheme
        $this->scheme = [];
        
        if (array_key_exists(self::FIELDS, $scheme)) {
            $this->scheme[self::FIELDS] = $this->getEntityFields();
            
            if (!empty($scheme[self::FIELDS])) {
                $this->scheme[self::FIELDS] = array_intersect(
                    $this->scheme[self::FIELDS],
                    $scheme[self::FIELDS]
                );
            }
        }
        
        foreach ($this->prepareExtras() as $index => $source) {
            // skip?
            if (array_key_exists($index, $scheme)) {
                if (is_array($source)) {
                    foreach ($source as $id => $item) {
                        $this->scheme[$index] = array_keys($item->getData());
                        break;
                    }
                } else {
                    $this->scheme[$index] = $this->getEntityFields($source);
                }
                
                if (!empty($scheme[$index])) {
                    $this->scheme[$index] = array_intersect(
                        $this->scheme[$index],
                        $scheme[$index]
                    );
                }
            }

        }
        
        foreach ($this->childEntities as $childEntity) {
            $childEntity->setDataScheme($scheme[$childEntity::ENTITY_CODE] ?? []);
        }
    }
    
    /**
     * Get extras
     *
     * @return array
     */
    protected function prepareExtras()
    {
        return [];
    }
    
    /**
     * Init self entity
     *
     * @return object
     */
    protected function initEntity()
    {
        return $this->objectManager->create($this->model);
    }
    
    /**
     * Returns entity
     *
     * @return object
     */
    public function getEntity()
    {
        if (!$this->modelEntity) {
            $this->modelEntity = $this->initEntity();
        }
        
        return $this->modelEntity;
    }
    
    /**
     * Validate data of current entity
     * throw exception on validation fails
     *
     * @param array $data
     */
    protected function validateEntity($data)
    {
        // throw exception on any error here
        return;
    }

    public function saveEntity($extraData)
    {
        if ($this->getParent()) {
            //try to re-set info for creditmemo, shipment, invoice. extrafields will be ignored on DB saving
            $this->modelEntity->setData('shipping_address_id', $this->getParent()->getEntity()->getShippingAddress()->getId());
            $this->modelEntity->setData('billing_address_id', $this->getParent()->getEntity()->getBillingAddress()->getId());
            $this->modelEntity->setData('customer_id', $this->getParent()->getEntity()->getCustomerId());
        }
        $this->modelEntity->save();
    }

    /**
     * Validate entity & subentities data
     *
     * @param array $data
     */
    public function validate($data)
    {
        $this->validateEntity($data);
        $this->treatChildren($data, self::CHILD_ACTION_VALIDATE);
        
        return $this;
    }
    
    /**
     * Save data to database
     *
     * @param array $data
     */
    public function save($data)
    {
        $this->data = $data;

        $this->modelEntity = $this->initEntity();
        $this->modelEntity->setData($data[self::FIELDS]);
        
        $this->saveEntity($data);

        $this->treatChildren($data, self::CHILD_ACTION_SAVE);
    }
    
    /**
     * Retrieve or modify full entity data
     *
     * @param array $filter
     * @return array
     */
    protected function retrieveEntity($filter)
    {
        $scheme = $this->getDataScheme();
        
        foreach ($this->prepareExtras() as $index => $source) {
            // skip?
            if (is_array($source)) {
                foreach ($source as $id => $item) {
                    $itemData = array_intersect_key($item->getData(), array_flip($scheme[$index]));
                    if ($itemData) {
                        $this->data[$index][$id] = $itemData;
                    }
                    break;
                }
            } else {
                $collection = $this->prepareFields($source, $scheme[$index]);
                if ($collection->count()) {
                    $this->data[$index] = $collection->getData();
                }
            }
        }
        
        return $filter;
    }
    
    /**
     * Load data
     *
     * @param array $data
     */
    public function retrieve($filter)
    {
        $return = [];
        $scheme = $this->getDataScheme();
        
        $collection = $this->prepareFields(
            $this->listByCondition($filter),
            $scheme[self::FIELDS] ?? []
        );

        foreach ($collection as $item) {
            $this->data        = [];
            $this->modelEntity = $item;
            
            if (array_key_exists(self::FIELDS, $scheme)) {
                $this->data[self::FIELDS] = $item->getData();
            }
            
            $subFilter = $this->retrieveEntity($filter);
            
            $this->treatChildren($subFilter, self::CHILD_ACTION_RETRIEVE);
            
            $return[] = $this->data;
        }
        
        return $return;
    }
    
    /**
     * Process child entities
     *
     * @param array $data
     */
    private function treatChildren($data, $action)
    {
        foreach ($this->childEntities as $childEntity) {
            // children of one type
            $stack = $data[$childEntity::ENTITY_CODE] ?? [];
            
            $childEntity->setParent($this);
            
            foreach ($stack as $single) {
                switch ($action) {
                    case self::CHILD_ACTION_VALIDATE:
                        $childEntity->validate($single);
                        break;
                    
                    case self::CHILD_ACTION_SAVE:
                        $childEntity->save($single);
                        break;
                    
                    case self::CHILD_ACTION_RETRIEVE:
                        $childData = $childEntity->retrieve($single);
                        if (!empty($childData)) {
                            // for retrieve count($stack)=1 ALWAYS
                            $this->data[$childEntity::ENTITY_CODE] = $childData;
                        }
                        break;
                }
            }
        }
    }
    
    /**
     * @param int $store
     * @return int
     */
    public function correctStore($store = null)
    {
        //$storeIds = $this->store->getCollection()->getAllIds();
        $stores = $this->storeCollectionFactory->create()->setWithoutDefaultFilter()->load()->toOptionHash();

        if (in_array($store, array_keys($stores)) && $this->config->getTryStoreviews()) {
            $storeId = $store;
        } else {
            $storeId = $this->config->getStoreId();
        }
        
        $storeName = isset($stores[$storeId]) ? $stores[$storeId] : null;

        return ['store_id' => $storeId, 'name' => $storeName];
    }
}
