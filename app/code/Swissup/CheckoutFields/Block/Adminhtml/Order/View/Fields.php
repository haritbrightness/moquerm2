<?php
namespace Swissup\CheckoutFields\Block\Adminhtml\Order\View;

class Fields extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * Field values collection factory
     * @var \Swissup\CheckoutFields\Model\ResourceModel\Field\Value\CollectionFactory
     */
    public $fieldValueCollectionFactory;

    /**
     * Field option factory
     * @var \Swissup\CheckoutFields\Model\ResourceModel\Field\Option\CollectionFactory
     */
    public $fieldOptionCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Config\Model\Config\Source\YesnoFactory
     */
    protected $yesnoFactory;

    /**
     * @var \Swissup\CheckoutFields\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Swissup\CheckoutFields\Model\ResourceModel\Field\Value\CollectionFactory $fieldValueCollectionFactory
     * @param \Swissup\CheckoutFields\Model\ResourceModel\Field\Option\CollectionFactory $fieldOptionCollectionFactory
     * @param \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Swissup\CheckoutFields\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Swissup\CheckoutFields\Model\ResourceModel\Field\Value\CollectionFactory $fieldValueCollectionFactory,
        \Swissup\CheckoutFields\Model\ResourceModel\Field\Option\CollectionFactory $fieldOptionCollectionFactory,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Swissup\CheckoutFields\Helper\Data $helper,
        array $data = []
    ) {
        $this->fieldValueCollectionFactory = $fieldValueCollectionFactory;
        $this->fieldOptionCollectionFactory = $fieldOptionCollectionFactory;
        $this->localeDate = $context->getLocaleDate();
        $this->yesnoFactory = $yesnoFactory;
        $this->helper = $helper;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Get link to edit checkout fields page
     *
     * @param string $label
     * @return string
     * @todo implement fields edit form
     */
    public function getEditLink($label = '')
    {
        return '';

        if ($this->_authorization->isAllowed('Magento_Sales::actions_edit')) {
            if (empty($label)) {
                $label = __('Edit');
            }
            $url = $this->getUrl(
                'checkoutfields/field/order',
                ['order_id' => $this->getOrder()->getId()]
            );
            return '<a href="' . $url . '">' . $label . '</a>';
        }
    }

    public function getFields($order = null)
    {
        if (!$this->helper->isEnabled()) {
            return null;
        }

        if (!$order) {
            $order = $this->getOrder();
        }
        $storeId = $order->getStore()->getId();

        $fields = $this->fieldValueCollectionFactory
            ->create()
            ->addEmptyValueFilter()
            ->addOrderFilter($order->getId())
            ->addStoreLabel($storeId);

        foreach ($fields as $field) {
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
                $field->setValue($yesnoValues[$field->getValue()]);
            } else if ($field->getFrontendInput() == 'select' ||
                $field->getFrontendInput() == 'multiselect')
            {
                $options = $this->fieldOptionCollectionFactory->create()
                    ->setStoreFilter($storeId)
                    ->setIdFilter(explode(',', $field->getValue()))
                    ->getColumnValues('value');

                $field->setValue($options);
            }
        }

        return $fields;
    }
}
