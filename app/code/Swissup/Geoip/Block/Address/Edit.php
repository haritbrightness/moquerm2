<?php

namespace Swissup\Geoip\Block\Address;

use Magento\Customer\Api\Data\AddressInterface;

class Edit extends \Magento\Customer\Block\Address\Edit
{
    /**
     * @var \Swissup\Geoip\Helper\Data
     */
    protected $geoip;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Swissup\Geoip\Helper\Data geoip
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Swissup\Geoip\Helper\Data $geoip,
        array $data = []
    ) {
        $this->geoip = $geoip;

        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $customerSession,
            $addressRepository,
            $addressDataFactory,
            $currentCustomer,
            $dataObjectHelper,
            $data
        );
    }

    /**
     * Prepare the layout of the address edit block.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->geoip->isEnabled()
            && $this->_address instanceof AddressInterface
            && !$this->_address->getId()
        ) {
            $record = $this->geoip->detect();

            if ($record->isValid()) {
                $data = [
                    'city' => $this->_address->getCity(),
                    'region' => $this->_address->getRegion(),
                    'region_id' => $this->_address->getRegionId(),
                    'postcode' => $this->_address->getPostcode(),
                    'country_id' => $this->_address->getCountryId()
                ];
                $data = array_filter($data);
                $data = array_merge($record->toAddressArray(), $data);
                $this->dataObjectHelper->populateWithArray(
                    $this->_address,
                    $data,
                    '\Magento\Customer\Api\Data\AddressInterface'
                );
            }
        }

        return $this;
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        $template = $this->_template;
        if (!strstr($template, '::')) {
            $template = 'Magento_Customer::' . $template;
        }
        return $template;
    }
}
