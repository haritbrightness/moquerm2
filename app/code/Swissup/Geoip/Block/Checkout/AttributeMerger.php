<?php

namespace Swissup\Geoip\Block\Checkout;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Helper\Address as AddressHelper;

use Swissup\Geoip\Helper\Data as GeoHelper;

class AttributeMerger extends \Magento\Checkout\Block\Checkout\AttributeMerger
{
    /**
     *
     * @var GeoHelper
     */
    protected $geoip;

    /**
     * @param AddressHelper $addressHelper
     * @param Session $customerSession
     * @param CustomerRepository $customerRepository
     * @param DirectoryHelper $directoryHelper
     * @param \Swissup\Geoip\Helper\Data geoip
     */
    public function __construct(
        AddressHelper $addressHelper,
        Session $customerSession,
        CustomerRepository $customerRepository,
        DirectoryHelper $directoryHelper,
        GeoHelper $geoip
    ) {
        parent::__construct($addressHelper, $customerSession, $customerRepository, $directoryHelper);

        $this->geoip = $geoip;
    }

    /**
     * @param string $attributeCode
     * @return null|string
     */
    protected function getDefaultValue($attributeCode): ?string
    {
        if ($this->geoip->isEnabled()) {
            $record = $this->geoip->detect();
            if ($record->isValid()) {
                switch ($attributeCode) {
                    case 'city':
                        return $record->getCityName();
                    case 'region_id':
                        return $record->getRegion()->getId();
                    case 'country_id':
                        return $record->getCountryCode();
                    case 'postcode':
                        return $record->getPostalCode();
                }
            }
        }

        return parent::getDefaultValue($attributeCode);
    }
}
