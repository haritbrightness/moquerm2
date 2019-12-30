<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */


namespace Aitoc\OrdersExportImport\Plugin\InventoryShipping;

use Magento\Framework\Event\Observer as EventObserver;

class SourceDeductionProcessor
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magento\InventoryShipping\Observer\SourceDeductionProcessor $subject
     * @param \Closure $proceed
     * @param EventObserver $observer
     * @return array
     */
    public function aroundExecute(
        \Magento\InventoryShipping\Observer\SourceDeductionProcessor $subject,
        \Closure $proceed,
        EventObserver $observer
    ) {
        $shipment = $observer->getEvent()->getShipment();

        if ($shipment && $shipment->getIsAitocImported()) {
            return $this;
        }

        return $proceed($observer);
    }
}
