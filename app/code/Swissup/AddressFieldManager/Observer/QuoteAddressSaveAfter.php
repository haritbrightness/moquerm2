<?php
namespace Swissup\AddressFieldManager\Observer;

class QuoteAddressSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swissup\AddressFieldManager\Model\Address\Quote
     */
    protected $quoteAddressModel;

    /**
     * @param \Swissup\AddressFieldManager\Model\Address\QuoteFactory $quoteAddressFactory
     */
    public function __construct(
        \Swissup\AddressFieldManager\Model\Address\QuoteFactory $quoteAddressFactory
    ) {
        $this->quoteAddressModel = $quoteAddressFactory->create();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $address = $observer->getEvent()->getQuoteAddress();
        $this->quoteAddressModel
            ->addData($address->getData())
            ->setId($address->getId())
            ->save();

        return $this;
    }
}
