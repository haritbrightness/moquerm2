<?php
namespace Swissup\DeliveryDate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

use Swissup\DeliveryDate\Model\DeliverydateFactory;

class SalesEventQuoteSubmitSuccessObserver implements ObserverInterface
{
    /**
     * @var DeliverydateFactory
     */
    protected $deliverydateFactory;

    /**
     * @param DeliverydateFactory $deliverydateFactory
     */
    public function __construct(DeliverydateFactory $deliverydateFactory)
    {
        $this->deliverydateFactory = $deliverydateFactory;
    }

    public function execute(EventObserver $observer)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var  \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $modelDeliveryDate = $this->deliverydateFactory
            ->create()
            ->loadByQuoteId($quote->getId());

        if ($modelDeliveryDate->getId()) {
            $modelDeliveryDate
                ->setOrderId($order->getId())
                ->save();
        }

        return $this;
    }
}
