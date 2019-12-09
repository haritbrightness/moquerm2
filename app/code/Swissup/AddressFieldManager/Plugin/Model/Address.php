<?php

namespace Swissup\AddressFieldManager\Plugin\Model;

use Swissup\AddressFieldManager\Model\ResourceModel\Customer\Form\AddressAttribute\CollectionFactory;

class Address
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Directory data
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryData = null;

    /**
     * @param CollectionFactory $addressFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        \Magento\Directory\Helper\Data $directoryData
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->directoryData = $directoryData;
    }

    /**
     * Standard validate method uses hardcoded validation.
     * We need to use actual field state to know which fields are required.
     *
     * @param  \Magento\Customer\Model\Address\AbstractAddress $subject
     * @param  \Closure $proceed
     * @return boolean
     */
    public function aroundValidate(
        \Magento\Customer\Model\Address\AddressModelInterface $subject,
        \Closure $proceed
    ) {
        if ($subject->getShouldIgnoreValidation()) {
            return true;
        }

        $errors = [];
        $collection = $this->collectionFactory->create();
        foreach ($collection as $attribute) {
            if (!$attribute->getIsRequired() || !$attribute->getIsVisible()) {
                continue;
            }

            $code  = $attribute->getAttributeCode();
            $value = $subject->getData($code);
            if ('street' === $attribute->getAttributeCode()) {
                $value = $subject->getStreetLine(1);
            }

            if (!\Zend_Validate::is($value, 'NotEmpty')) {
                $errors[] = __('%fieldName is a required field.', [
                    'fieldName' => $code
                ]);
            }
        }

        $havingOptionalZip = $this->directoryData->getCountriesWithOptionalZip();
        if (!in_array($subject->getCountryId(), $havingOptionalZip)
            && !\Zend_Validate::is($subject->getPostcode(), 'NotEmpty')) {

            $errors[] = __('%fieldName is a required field.', [
                'fieldName' => 'postcode'
            ]);
        }

        if ($subject->getCountryModel()->getRegionCollection()->getSize()
            && !\Zend_Validate::is($subject->getRegionId(),'NotEmpty')
            && $this->directoryData->isRegionRequired($subject->getCountryId())) {

            $errors[] = __('%fieldName is a required field.', [
                'fieldName' => 'region'
            ]);
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}
