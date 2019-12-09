<?php
namespace Swissup\AddressAutocomplete\Model\Config\Source;

use Magento\Customer\Api\AddressMetadataInterface;

class StreetNumberPlacement implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Form\Attribute\Collection
     */
    private $collection;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Form\Attribute\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Form\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->collection = $collectionFactory->create();
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [
            ['value' => 'line1_start', 'label' => __('Address Line 1 Start')],
            ['value' => 'line1_end', 'label' => __('Address Line 1 End')],
            ['value' => 'line2', 'label' => __('Address Line 2')],
        ];

        $this->collection
            ->addFieldToFilter('frontend_input', ['neq' => 'hidden'])
            ->setEntityType(AddressMetadataInterface::ENTITY_TYPE_ADDRESS)
            ->addFormCodeFilter('adminhtml_customer_address')
            ->addFieldToFilter('is_user_defined', 1);

        foreach ($this->collection as $item) {
            $result[] = [
                'value' => 'custom_attributes[' . $item->getAttributeCode() . ']',
                'label' => $item->getFrontendLabel() . ' [' . $item->getAttributeCode() . ']',
            ];
        }

        return $result;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'line1_start' => __('Address Line 1 Start'),
            'line1_end' => __('Address Line 1 End'),
            'line2' => __('Address Line 2'),
        ];
    }
}
