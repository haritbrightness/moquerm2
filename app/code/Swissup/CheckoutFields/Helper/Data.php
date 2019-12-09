<?php
namespace Swissup\CheckoutFields\Helper;

use Magento\Store\Model\ScopeInterface;
use Swissup\CheckoutFields\Api\Data\FieldDataInterfaceFactory;
use Swissup\CheckoutFields\Model\ResourceModel\Field\Option\CollectionFactory as OptionCollectionFactory;
use Swissup\CheckoutFields\Model\ResourceModel\Field\Value\CollectionFactory as ValueCollectionFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $formElementMap = [
        'text' => 'Magento_Ui/js/form/element/abstract',
        'textarea' => 'Magento_Ui/js/form/element/textarea',
        'select' => 'Magento_Ui/js/form/element/select',
        'boolean' => 'Magento_Ui/js/form/element/select',
        'multiselect' => 'Magento_Ui/js/form/element/checkbox-set',
        'date' => 'Magento_Ui/js/form/element/date'
    ];

    protected $templateMap = [
        'text' => 'ui/form/element/input',
        'textarea' => 'ui/form/element/textarea',
        'select' => 'ui/form/element/select',
        'boolean' => 'ui/form/element/select',
        'multiselect' => 'ui/form/element/checkbox-set',
        'date' => 'ui/form/element/date'
    ];

    /**
     * Checkout field options collection factory
     * @var OptionCollectionFactory
     */
    protected $fieldOptionsCollectionFactory;

    /**
     * Field values collection factory
     * @var ValueCollectionFactory
     */
    protected $fieldValueCollectionFactory;

    /**
     * Field data factory
     * @var FieldDataInterfaceFactory
     */
    protected $fieldDataInterfaceFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Config\Model\Config\Source\YesnoFactory
     */
    protected $yesnoFactory;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param OptionCollectionFactory $fieldOptionsCollectionFactory
     * @param ValueCollectionFactory $fieldValueCollectionFactory
     * @param FieldDataInterfaceFactory $fieldDataInterfaceFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        OptionCollectionFactory $fieldOptionsCollectionFactory,
        ValueCollectionFactory $fieldValueCollectionFactory,
        FieldDataInterfaceFactory $fieldDataInterfaceFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->fieldOptionsCollectionFactory = $fieldOptionsCollectionFactory;
        $this->fieldValueCollectionFactory = $fieldValueCollectionFactory;
        $this->fieldDataInterfaceFactory = $fieldDataInterfaceFactory;
        $this->localeDate = $localeDate;
        $this->yesnoFactory = $yesnoFactory;
        $this->assetRepo = $assetRepo;
        parent::__construct($context);
    }

    /**
     * Path to store config checkout fields enabled
     *
     * @var string
     */
    const XML_PATH_ENABLED = 'checkoutfields/general/enabled';

    /**
     * Get config value by key
     * @param  string $key config path
     * @return string
     */
    protected function _getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Module is enabled config
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)$this->_getConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Get field Ui component by frontend type
     * @param \Swissup\CheckoutFields\Model\Field $field
     * @return string
     */
    public function getFieldUiComponent($field)
    {
        return $this->formElementMap[$field->getFrontendInput()];
    }

    /**
     * Get field template by frontend type
     * @param \Swissup\CheckoutFields\Model\Field $field
     * @return string
     */
    public function getFieldTemplate($field)
    {
        return $this->templateMap[$field->getFrontendInput()];
    }

    /**
     * Get field component
     * @param  \Swissup\CheckoutFields\Model\Field $field
     * @param  string $label
     * @param  array $validation
     * @param  string $default
     * @param  array $options
     * @param  string $provider
     * @return array
     */
    public function getFieldComponent($field, $label, $validation, $default, $options, $provider)
    {
        return [
            'component' => $this->getFieldUiComponent($field),
            'config' => $this->getComponentConfig($field),
            'options' => $options,
            'caption' => __('Please select'),
            'dataScope' => 'swissupCheckoutFields.swissup_checkout_field[' . $field->getAttributeCode() . ']',
            'label' => $label,
            'provider' => $provider,
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
        $config = [
            'id' => $field->getAttributeCode(),
            'template' => 'ui/form/field',
            'customScope' => 'swissupCheckoutFields',
            'elementTmpl' => $this->getFieldTemplate($field),
            'multiple' => $field->getFrontendInput() == 'multiselect',
            'additionalClasses' => 'swissup-checkout-fields__field'
        ];
        if ($field->getFrontendInput() == 'date') {
            $config['inputDateFormat'] = $this->getDateFormat();
        }

        return $config;
    }

    /**
     * Get checkout field default value(s)
     * @param  \Swissup\CheckoutFields\Model\Field $field
     * @return string|array
     */
    public function getDefaultValue($field)
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
    public function getFieldOptions($field, $storeId)
    {
        if ($field->getFrontendInput() == 'date') {
            $options = [
                'dateFormat' => $this->getDateFormat(),
                'showOn' => 'both',
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '-100y:+10y',
                'buttonImage' => $this->getViewFileUrl('Magento_Theme::calendar.png')
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

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @return string
     */
    public function getViewFileUrl($fileId)
    {
        return $this->assetRepo->getUrlWithParams($fileId, [
            '_secure' => $this->_getRequest()->isSecure()
        ]);
    }

    /**
     * Get date format for current locale
     * @return string
     */
    public function getDateFormat()
    {
        return $this->localeDate->getDateFormat(\IntlDateFormatter::SHORT);
    }

    /**
     * Get checkout fields values for order
     * @param  \Magento\Sales\Api\Data\OrderInterface $order
     * @param  array|null $selectedFields
     * @return \Swissup\CheckoutFields\Api\Data\FieldDataInterface[]|null
     */
    public function getOrderFieldsValues($order, $selectedFields = null)
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $storeId = $order->getStore()->getId();
        $fieldsData = [];
        $fields = $this->fieldValueCollectionFactory
            ->create()
            ->addEmptyValueFilter()
            ->addOrderFilter($order->getId())
            ->addStoreLabel($storeId);

        foreach ($fields as $field) {
            if ($selectedFields && !in_array($field->getAttributeCode(), $selectedFields)) {
                continue;
            }

            if ($field->getFrontendInput() == 'date') {
                $formattedDate = $this->localeDate->formatDate(
                    $this->localeDate->scopeDate(
                        $order->getStore(),
                        $field->getValue()
                    ),
                    \IntlDateFormatter::MEDIUM,
                    false
                );
                $field->setValue($formattedDate);
            } elseif ($field->getFrontendInput() == 'boolean') {
                $yesnoValues = $this->yesnoFactory->create()->toArray();
                $field->setValue($yesnoValues[$field->getValue()]->getText());
            } else if ($field->getFrontendInput() == 'select' ||
                $field->getFrontendInput() == 'multiselect')
            {
                $options = $this->fieldOptionsCollectionFactory->create()
                    ->setStoreFilter($storeId)
                    ->setIdFilter(explode(',', $field->getValue()))
                    ->getColumnValues('value');

                $field->setValue($options);
            }

            $fieldDataObject = $this->fieldDataInterfaceFactory->create();
            $fieldDataObject->setCode($field->getAttributeCode());
            $fieldDataObject->setLabel($field->getStoreLabel());
            $fieldDataObject->setValue($field->getValue());

            $fieldsData[] = $fieldDataObject;
        }

        return $fieldsData;
    }
}
