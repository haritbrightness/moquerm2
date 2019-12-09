<?php

namespace Swissup\AddressFieldManager\Plugin\Model;

use Magento\Framework\Exception\InputException;

/**
 * This class can be removed, when the following patch will be included in
 * magento:
 *
 * https://github.com/magento/magento2/pull/7552
 *
 */
class AddressRepository
{
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Model\AddressRegistry
     */
    protected $addressRegistry;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {
        $this->addressFactory = $addressFactory;
        $this->addressRegistry = $addressRegistry;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * This class can be removed, when the following patch will be included in
     * magento:
     *
     * https://github.com/magento/magento2/pull/7552
     *
     * @param  \Magento\Customer\Model\Address\AddressModelInterface $subject
     * @param  \Closure $proceed
     * @return boolean
     */
    public function aroundSave(
        \Magento\Customer\Api\AddressRepositoryInterface $subject,
        \Closure $proceed,
        \Magento\Customer\Api\Data\AddressInterface $address
    ) {
        $addressModel = null;
        $customerModel = $this->customerRegistry->retrieve($address->getCustomerId());
        if ($address->getId()) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
        }

        if ($addressModel === null) {
            $addressModel = $this->addressFactory->create();
            $addressModel->updateData($address);
            $addressModel->setCustomer($customerModel);
        } else {
            $addressModel->updateData($address);
        }

        $errors = $addressModel->validate();
        if ($errors !== true) {
            $inputException = new InputException();
            foreach ($errors as $error) {
                $inputException->addError($error);
            }
            throw $inputException;
        }
        $addressModel->save();
        $address->setId($addressModel->getId());
        // Clean up the customer registry since the Address save has a
        // side effect on customer : \Magento\Customer\Model\ResourceModel\Address::_afterSave
        $this->customerRegistry->remove($address->getCustomerId());
        $this->addressRegistry->push($addressModel);
        $customerModel->getAddressesCollection()->clear();

        return $addressModel->getDataModel();
    }
}
