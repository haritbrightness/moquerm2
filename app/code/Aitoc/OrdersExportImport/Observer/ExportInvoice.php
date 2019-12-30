<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Observer;

use Aitoc\OrdersExportImport\Model\Profile;
use Aitoc\OrdersExportImport\Model\ResourceModel\Profile\CollectionFactory as ProfileCollectionFactory;
use Magento\Sales\Model\Order\Invoice\ItemFactory as InvoiceItemFactory;

/**
 * Class ExportInvoice
 */
class ExportInvoice extends AbstractObserver
{
    /**
     * @var InvoiceItemFactory
     */
    protected $invoiceItemFactory;
    
    /**
     * Constructor.
     *
     * @param ProfileCollectionFactory $profileCollectionFactory
     * @param InvoiceItemFactory $InvoiceItemFactory
     */
    public function __construct(
        ProfileCollectionFactory $profileCollectionFactory,
        InvoiceItemFactory $invoiceItemFactory
    ) {
        parent::__construct($profileCollectionFactory);
        
        $this->invoiceItemFactory = $invoiceItemFactory;
    }
    
    /**
     * Check order invoice
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function isAllItemsInvoiced($order)
    {
        $orderItems   = [];
        $invoiceItems = [];
        
        foreach ($order->getItemsCollection() as $item) {
            if (!$item->isDummy()) {
                $orderItems[] = $item->getId();
            }
        }
        
        $items = $this->invoiceItemFactory->create();
        $items->getCollection()
            ->addFieldToFilter('order_item_id', ['in' => $orderItems]);

        foreach ($items as $item) {
            if ($item->getBasePrice()) {
                $invoiceItems[] = $item->getOrderItemId();
            }
        }
        
        return (bool)count(array_diff($orderItems, $invoiceItems));
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoiceItem = $observer->getEvent()->getData('invoice_item');
        $order       = $invoiceItem->getInvoice()->getOrder();
        
        if ($order->getId() && $this->isAllItemsInvoiced($order)) {
            $profileCollection = $this->profileCollectionFactory->create();
            
            // collect profile ids for checkout export
            foreach ($profileCollection as $profile) {
                if ($profile->getConfig()->getEntity() == 'order' &&
                    $profile->getConfig()->getExportType() == Profile::TYPE_INVOICE
                ) {
                    $profile->updateTasksWithData($order->getId());
                }
            }
        }

        return $this;
    }
}
