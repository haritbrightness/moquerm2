<?php
namespace Swissup\DeliveryDate\Observer\Adminhtml\Order\Shipping;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

use Swissup\DeliveryDate\Model\DeliverydateFactory;

class ViewObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var DeliverydateFactory
     */
    protected $deliverydateFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     *
     * @param TimezoneInterface $timezone
     * @param DeliverydateFactory $deliverydateFactory
     */
    public function __construct(
        TimezoneInterface $timezone,
        DeliverydateFactory $deliverydateFactory
    ) {
        $this->timezone = $timezone;
        $this->deliverydateFactory = $deliverydateFactory;
    }

    public function execute(EventObserver $observer)
    {
        if ($observer->getElementName() != 'order_shipping_view') {
            return;
        }

        $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
        $order = $orderShippingViewBlock->getOrder();
        $formattedDate = 'N/A';
        $deliveryTime = '';

        $deliveryDate = $this->deliverydateFactory
            ->create()
            ->loadByOrderId($order->getId());

        if ($deliveryDate->getId()) {
            $date = $deliveryDate->getDate();
            $formattedDate = $this->timezone->formatDate(
                $this->timezone->scopeDate($order->getStore(), $date, true),
                \IntlDateFormatter::MEDIUM,
                false
            );
            $deliveryTime = $deliveryDate->getTimerange();
        }

        $deliveryDateBlock = $observer->getLayout()->createBlock(
            'Magento\Framework\View\Element\Template'
        );
        $deliveryDateBlock->setDeliveryDate($formattedDate);
        $deliveryDateBlock->setDeliveryTime($deliveryTime);
        $deliveryDateBlock->setTemplate('Swissup_DeliveryDate::order/shipping/view.phtml');
        $html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();

        $observer->getTransport()->setOutput($html);
    }
}
