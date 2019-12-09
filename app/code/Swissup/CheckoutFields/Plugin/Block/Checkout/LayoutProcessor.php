<?php
namespace Swissup\CheckoutFields\Plugin\Block\Checkout;

class LayoutProcessor
{
    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Checkout fields collection factory
     * @var \Swissup\CheckoutFields\Model\ResourceModel\Field\CollectionFactory
     */
    protected $fieldsCollectionFactory;

    /**
     * Checkout field options collection factory
     * @var \Swissup\CheckoutFields\Model\ResourceModel\Field\Option\CollectionFactory
     */
    protected $fieldOptionsCollectionFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\YesnoFactory
     */
    protected $yesnoFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Checkout fields helper
     * @var \Swissup\CheckoutFields\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\CheckoutFields\Model\ResourceModel\Field\CollectionFactory $fieldsCollectionFactory
     * @param \Swissup\CheckoutFields\Model\ResourceModel\Field\Option\CollectionFactory $fieldOptionsCollectionFactory
     * @param \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Swissup\CheckoutFields\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\CheckoutFields\Model\ResourceModel\Field\CollectionFactory $fieldsCollectionFactory,
        \Swissup\CheckoutFields\Model\ResourceModel\Field\Option\CollectionFactory $fieldOptionsCollectionFactory,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Swissup\CheckoutFields\Helper\Data $helper
    ) {
        $this->storeManager = $storeManager;
        $this->fieldsCollectionFactory = $fieldsCollectionFactory;
        $this->fieldOptionsCollectionFactory = $fieldOptionsCollectionFactory;
        $this->yesnoFactory = $yesnoFactory;
        $this->localeDate = $localeDate;
        $this->helper = $helper;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $jsLayout
    ) {
        if(!$this->helper->isEnabled()) {
            return $jsLayout;
        }

        $storeId = $this->storeManager->getStore()->getId();
        $fields = $this->fieldsCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addIsActiveFilter(1)
            ->addOrder(
                \Swissup\CheckoutFields\Api\Data\FieldInterface::SORT_ORDER,
                \Magento\Framework\Data\Collection::SORT_ORDER_ASC
            );

        if ($fields->getSize()) {
            $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['beforeMethods']
                ['children']['swissup-checkout-fields'] =
                [
                    'component' => 'uiComponent',
                    'config' => [
                        'template' => 'Swissup_CheckoutFields/container'
                    ],
                    'visible' => true,
                    'sortOrder' => 0
                ];
        }

        foreach ($fields as $field) {
            $label = $field->getStoreLabel($storeId);

            $validation = [];
            if ($field->getIsRequired() == 1) {
                if ($field->getFrontendInput() == 'multiselect') {
                    $validation['validate-one-required'] = true;
                }
                $validation['required-entry'] = true;
            }

            $options = $this->getFieldOptions($field, $storeId);
            $default = $this->getDefaultValue($field);

            $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['beforeMethods']
                ['children']['swissup-checkout-fields']['children'][$field->getAttributeCode()] =
                $this->getFieldComponent($field, $label, $validation, $default, $options);
        }
        return $jsLayout;
    }

    /**
     * Get field component
     * @param  \Swissup\CheckoutFields\Model\Field $field
     * @param  string $label
     * @param  array $validation
     * @param  string $default
     * @param  array $options
     * @return array
     */
    protected function getFieldComponent($field, $label, $validation, $default, $options)
    {
        return [
            'component' => $this->helper->getFieldUiComponent($field),
            'config' => $this->getComponentConfig($field),
            'options' => $options,
            'caption' => __('Please select'),
            'dataScope' => 'swissupCheckoutFields.swissup_checkout_field[' . $field->getAttributeCode() . ']',
            'label' => $label,
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => $validation,
            'sortOrder' => $field->getSortOrder(),
            'id' => 'swissup_checkout_field[' . $field->getAttributeCode() . ']',
            'value' => $default
        ];
    }

    /**
     * Get UIComponent config
     * @param  \Swissup\CheckoutFields\Model\Field $field
     * @return array
     */
    protected function getComponentConfig($field)
    {
        return [
            'id' => $field->getAttributeCode(),
            'template' => 'ui/form/field',
            'customScope' => 'swissupCheckoutFields',
            'elementTmpl' => $this->helper->getFieldTemplate($field),
            'multiple' => $field->getFrontendInput() == 'multiselect',
            'additionalClasses' => 'swissup-checkout-fields__field',
        ];
    }

    /**
     * Get checkout field default value(s)
     * @param  \Swissup\CheckoutFields\Model\Field $field
     * @return string|array
     */
    protected function getDefaultValue($field)
    {
        $default = $field->getDefaultValue();
        if ($field->getFrontendInput() == 'multiselect' && $default) {
            $default = explode(',', $field->getDefaultValue());
        }

        return $default;
    }

    /**
     * Get checkout field options
     * @param  \Swissup\CheckoutFields\Model\Field $field
     * @param  int $storeId
     * @return array
     */
    protected function getFieldOptions($field, $storeId)
    {
        if ($field->getFrontendInput() == 'date') {
            $dateFormat = $this->localeDate->getDateFormat(\IntlDateFormatter::SHORT);
            $options = [
                'dateFormat' => $dateFormat
            ];
        } elseif ($field->getFrontendInput() == 'boolean') {
            $options = $this->yesnoFactory->create()->toOptionArray();
        } else {
            $collection = $this->fieldOptionsCollectionFactory->create()
                ->setPositionOrder('asc')
                ->setAttributeFilter($field->getId())
                ->setStoreFilter($storeId)
                ->load();
            $options = $collection->getAllOptions();
        }

        return $options;
    }
}
