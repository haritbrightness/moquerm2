<?php
namespace Swissup\AddressFieldManager\Observer;

class OrderAddressSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swissup\AddressFieldManager\Model\Address\Order
     */
    protected $orderAddressModel;

    /**
     * @param \Swissup\AddressFieldManager\Model\Address\OrderFactory $orderAddressFactory
     */
    public function __construct(
        \Swissup\AddressFieldManager\Model\Address\OrderFactory $orderAddressFactory
    ) {
        $this->orderAddressModel = $orderAddressFactory->create();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        $this->orderAddressModel
            ->addData($address->getData())
            ->setId($address->getId())
            ->save();

        return $this;
    }
}
