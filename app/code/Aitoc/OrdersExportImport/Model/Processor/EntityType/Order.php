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

use Aitoc\OrdersExportImport\Model\Processor\EntityType\AbstractEntity;
use Magento\Store\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;

/**
 * Class Order
 */
class Order extends AbstractEntity
{
    /**
     * Entity Type unique code
     */
    const ENTITY_CODE = 'order';
    
    /**
     * Order default status
     */
    const DEFAULT_STATUS         = 'pending';
    const DEFAULT_SHIPPINGMETHOD = 'flatrate_flatrate';
    
    /**
     * Additional data items
     */
    const ORDER_ITEM          = 'item';
    const ORDER_ADDRESS       = 'address';
    const ORDER_PAYMENT       = 'payment';
    const ORDER_TRANSACTION   = 'paymenttransaction';
    const ORDER_STATUSHISTORY = 'statushistory';
    
    /**
     * Megento entity class to be imported
     */
    protected $model = \Magento\Sales\Model\Order::class;

    /**
     * Child entities to include to import process
     */
    protected $children = [
        \Aitoc\OrdersExportImport\Model\Processor\EntityType\Invoice::class,
        \Aitoc\OrdersExportImport\Model\Processor\EntityType\Creditmemo::class,
        \Aitoc\OrdersExportImport\Model\Processor\EntityType\Shipment::class,
    ];
    
    /**
     * @var Magento\Sales\Model\Order\Payment\RepositoryFactory
     */
    protected $paymentRepository;

    /**
     * @var Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @var Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;
    
    /**
     * @var Magento\Sales\Model\Order\AddressFactory
     */
    protected $orderAddressFactory;
    
    /**
     * @var Magento\Sales\Model\Order\ItemFactory
     */
    protected $itemFactory;
    
    /**
     * @var Magento\Sales\Model\Order\Status\HistoryFactory
     */
    protected $statusHistoryFactory;
    
    /**
     * @var Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory
     */
    private $transactionCollectionFactory;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * Constructor
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory $entityTypeFactory,
        \Aitoc\OrdersExportImport\Model\Processor\Config $config,
        StoreCollectionFactory $storeCollectionFactory,
        \Magento\Sales\Model\Order\Payment\Repository $paymentRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Sales\Model\Order\AddressFactory $orderAddressFactory,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Sales\Model\Order\Status\HistoryFactory $statusHistoryFactory,
        \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        parent::__construct($objectManager, $storeCollectionFactory, $entityTypeFactory, $config);
        $this->paymentRepository    = $paymentRepository;
        $this->customerFactory      = $customerFactory;
        $this->addressFactory       = $addressFactory;
        $this->orderAddressFactory  = $orderAddressFactory;
        $this->itemFactory          = $itemFactory;
        $this->statusHistoryFactory = $statusHistoryFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->json                 = $json;
    }
    
    /**
     * Retrieve or modify full entity data
     *
     * @param array $filter
     * @return array
     */
    protected function retrieveEntity($filter)
    {
        $filter = parent::retrieveEntity($filter);
        $scheme = $this->getDataScheme();
        
        foreach ($this->childEntities as $childEntity) {
            if (array_key_exists($childEntity::ENTITY_CODE, $scheme)) {
                $fields = $childEntity->getEntityFields();
                
                if (in_array('parent_id', $fields)) {
                    $entityParentField = 'parent_id';
                } elseif (in_array('order_id', $fields)) {
                    $entityParentField = 'order_id';
                } else {
                    continue;
                }
                
                $filter[$childEntity::ENTITY_CODE][] = [
                    [
                        $entityParentField => ['eq' => $this->getEntity()->getEntityId()]
                    ]
                ];
            }
        }
        
        return $filter;
    }
    
    /**
     * Get extras
     *
     * @return array
     */
    protected function prepareExtras()
    {
        $transactionCollection = $this->getEntity()->getId()
            ? $this->transactionCollectionFactory->create()->addOrderIdFilter($this->getEntity()->getId())
            : $this->transactionCollectionFactory->create();

        // Magento cache sub-collections itself
        // so repeate calls performs much faster
        $extra = [
            self::ORDER_ITEM          => $this->getEntity()->getItemsCollection(),
            self::ORDER_ADDRESS       => $this->getEntity()->getAddressesCollection(),
            self::ORDER_PAYMENT       => $this->getEntity()->getPaymentsCollection(),
            self::ORDER_STATUSHISTORY => $this->getEntity()->getStatusHistoryCollection(),
            self::ORDER_TRANSACTION   => $transactionCollection,
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
        $this->validateIncrement($data[self::FIELDS] ?? []);
        
        if (!isset($data[self::ORDER_ITEM])) {
            throw new \Exception(__('Order without items cannot be imported.'));
        }
        
        if (!isset($data[self::ORDER_PAYMENT])) {
            throw new \Exception(__('Order without payment cannot be imported.'));
        }
        
        if (!isset($data[self::ORDER_ADDRESS])) {
            throw new \Exception(
                __(
                    'An order with a missed address information can not be imported in Magento. '
                    . 'Please add these fields with values for them into your import file: '
                    . 'postcode, lastname, street, city, email, telephone, country_id, firstname, address_type'
                )
            );
        }
    }
    
    /**
     * @param array $entityData
     */
    private function validateIncrement($entityData)
    {
        if (empty($entityData['increment_id'])) {
            throw new \Exception(
                __("Some orders will not be imported because they are missing the increment_id value (order number).")
            );
        } elseif ($this->config->getImportBehavior() == 'append') {
            $object = $this->initEntity();
            $object->loadByIncrementIdAndStoreId($entityData['increment_id'], $this->correctStore($entityData['store_id'])['store_id']);;
            
            if ($object->getId()) {
                throw new \Exception(__('Order #%1 already exist.', $object->getIncrementId()));
            }
        }
    }
    
    /**
     * @param array $extraData
     */
    public function saveEntity($extraData)
    {
        $this->modelEntity->setEntityId(null);
        
        $store = $this->correctStore($this->modelEntity->getStoreId());
        $this->modelEntity->setStoreId($store['store_id']);
        $this->modelEntity->setStoreName($store['name']);

        $this->modelEntity->setShippingMethod(
            $this->modelEntity->getShippingMethod() ? : self::DEFAULT_SHIPPINGMETHOD
        );
        $newStatus = $this->modelEntity->getStatus() ? : self::DEFAULT_STATUS;
        
        if ($this->config->getImportBehavior() == 'replace') {
            $this->deleteChildrenEntities();
        }

        $this->addPayment($extraData[self::ORDER_PAYMENT]);
        $this->addAddressAccount($extraData[self::ORDER_ADDRESS]);
        $this->addItems($extraData[self::ORDER_ITEM]);
        $this->addStatusHistory($extraData[self::ORDER_STATUSHISTORY] ?? []);

        $this->modelEntity->setStatus($newStatus);
        parent::saveEntity($extraData);

        foreach ($extraData[self::ORDER_TRANSACTION] ?? [] as $transaction) {
            if ($transaction['payment_id'] == $this->modelEntity->getPayment()->getOldId()) {
                $transaction['transaction_id'] = null;
                $transaction['parent_id'] = null;
                if (isset($transaction['additional_information'])) {
                    $transaction['additional_information'] = ($transaction['additional_information'] == 'null')
                        ? null : $transaction['additional_information'];
                }
                $transaction = $this->transactionRepository->create()->setData($transaction);

                $transaction->setPaymentId($this->modelEntity->getPayment()->getId())
                    ->setPayment($this->modelEntity->getPayment())
                    ->setOrderId($this->modelEntity->getId())
                    ->setOrder($this->modelEntity);
                $this->modelEntity->getPayment()->setCreatedTransaction($transaction)->save();
                $transaction->save();
            }
        }
    }

    /**
     * @param array $paymentsData
     */
    private function addPayment($paymentsData)
    {
        foreach ($paymentsData as $payData) {
            if (array_key_exists('additional_information', $payData)
                && is_string($payData['additional_information'])) {
                $payData['additional_information'] = $this->unserialize($payData['additional_information']);
            }
            if (empty($payData['additional_information'])) {
                $payData['additional_information'] = [];
            }
            // set default additional_information
            if ($payData['additional_information']) {
                $payData['additional_information'] = array_fill_keys(
                    ['method_title', 'payable_to', 'mailing_address'],
                    null
                );
            }

            $oldId =  $payData['entity_id'];
            $payData['entity_id'] = null;
            $payData['parent_id'] = null;

            $payment = $this->paymentRepository->create();
            $payment->setData($payData)->setOldId($oldId);
            $this->modelEntity->setPayment($payment);
        }
    }

    /**
     * @param array $history
     */
    private function addStatusHistory($history)
    {
        foreach ($history as $item) {
            $item['entity_id'] = null;
            $item['parent_id'] = null;
            
            $store = $this->correctStore($item['store_id'] ?? null);
            $item['store_id'] = $store['store_id'];
            
            $status = $this->statusHistoryFactory->create();
            $status->setData($item);
            $this->modelEntity->addStatusHistory($status);
        }
    }

    /**
     * @param array $items
     */
    private function addItems($items)
    {
        $itemModels = [];
        foreach ($items as $item) {
            //json/serialize compatiblity
            foreach (['weee_tax_applied', 'product_options'] as $option) {
                if (isset($item[$option])) {
                    $item[$option] = $this->unserialize($item[$option]);
                }
            }
            if (isset($item['weee_tax_applied'])) {
                $item['weee_tax_applied'] = $this->json->serialize($item['weee_tax_applied']);
            }
            //correct store
            $store = $this->correctStore($item['store_id'] ?? null);
            $item['store_id'] = $store['store_id'];

            $item['product_id'] = null;
            $item['quote_item_id'] = null;

            $itemModel = $this->itemFactory->create();
            $itemModel->setData($item);
            $itemModel->setOldItemId($itemModel->getItemId())->setItemId(null);
            $itemModels[$item['item_id']] = $itemModel;
        }

        foreach ($itemModels as $itemModel) {
            if ($itemModel->getParentItemId() && !empty($itemModels[$itemModel->getParentItemId()])) {
               $itemModel->setParentItem($itemModels[$itemModel->getParentItemId()]);
               $itemModel->setParentItemId(null);
            }
        }
        //save parents of bundle/configurable products before children, so parent_item_id will be properly set
        usort($itemModels,function($a, $b) {
            return $a->getParentItem() ? 1 : -1;
        });
        foreach ($itemModels as $itemModel) {
            $this->modelEntity->addItem($itemModel);
        }
    }

    /**
     * @param string $line
     * @return mixed
     */
    private function unserialize($line)
    {
        try {
            $result = $this->json->unserialize($line);
        } catch (\Exception $e) {
            $result = @unserialize($line);
        }

        return $result;
    }
    
    /**
     * @param array $addresses
     */
    private function addAddressAccount($addresses)
    {
        foreach ($addresses as $address) {
            $email    = $this->modelEntity->getCustomerEmail() ? : $address['email'] ?? null;
            $customer = $this->customerFactory->create()
                ->getCollection()
                ->addFilter('email', $email)
                ->getFirstItem();
            
            if ($this->config->getCustomersAutocreate()) {
                // new account
                if (!$customer->getId()) {
                    $data = array_intersect_key($address, array_flip(['firstname', 'lastname', 'middlename']));
                    $data['email'] = $email;
                    $customer->setData($data);
                }
                
                if ($customer->getAddressCollection()->count() < 2) {
                    $customer->addAddress($this->prepareAddress($address));
                }
                
                $customer->save();
            }
            
            $this->modelEntity->setCustomerId($customer->getId());
            $this->modelEntity->setCustomerIsGuest(!(bool)$customer->getId());
            
            $address['entity_id'] = null;
            $address['parent_id'] = null;
            $address['customer_address_id'] = null;
            
            $modelAddress = $this->orderAddressFactory->create();
            $modelAddress->setData($address);
            $this->modelEntity->addAddress($modelAddress);
        }
    }
    
    /**
     * @param array $element
     * @return mixed
     */
    private function prepareAddress($element)
    {
        $address = $this->addressFactory->create();
        $newData = array_intersect_key(
            $element,
            array_flip([
                'firstname',
                'lastname',
                'middlename',
                'city',
                'region_id',
                'region',
                'postcode',
                'street',
                'telephone',
                'country_id',
                'company'
            ])
        );
        
        $address->setData($newData);

        return $address;
    }

    /**
     * @return void
     */
    private function deleteChildrenEntities()
    {
        $this->deleteCollection($this->modelEntity->getItemsCollection());
        $this->deleteCollection($this->modelEntity->getAddresses());
        if (is_object($this->modelEntity->getPayment())) {
            $this->modelEntity->getPayment()->delete();
        }
        $this->deleteCollection($this->modelEntity->getStatusHistories());
        $this->deleteCollection($this->modelEntity->getInvoiceCollection());
        $this->deleteCollection($this->modelEntity->getShipmentsCollection());
        $this->deleteCollection($this->modelEntity->getCreditmemosCollection());
    }

    /**
     * @param mixed $collection
     */
    private function deleteCollection($collection)
    {
        if (is_object($collection) && $collection->getSize()) {
            $collection->walk('delete');
        }
    }
}
