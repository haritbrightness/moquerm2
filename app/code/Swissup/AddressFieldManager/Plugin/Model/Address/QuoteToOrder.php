<?php
namespace Swissup\AddressFieldManager\Plugin\Model\Address;

use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Api\Data\OrderAddressInterface;

class QuoteToOrder
{
    /**
     * @var \Swissup\FieldManager\Helper\Data
     */
    private $helper;

    /**
     * @param \Swissup\FieldManager\Helper\Data $helper
     */
    public function __construct(
        \Swissup\FieldManager\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject
     * @param callable $proceed
     * @param Address $object
     * @param array $data
     * @return OrderAddressInterface
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject,
        callable $proceed,
        Address $object,
        $data = []
    ) {
        $result = $proceed($object, $data);
        $codes = $this->helper->getCustomAttributeCodes();
        foreach ($codes as $code) {
            $result->setData($code, $object->getData($code));
        }

        return $result;
    }
}
