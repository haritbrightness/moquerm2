<?php
namespace Swissup\CheckoutFields\Plugin\Ui\Component;

use Magento\Ui\Component\Listing\Columns as ListingColumns;
use Swissup\CheckoutFields\Model\ResourceModel\Field\CollectionFactory;

class OrderListingColumns
{

    /**
     * Checkout fields collection factory
     * @var CollectionFactory
     */
    protected $fieldsCollectionFactory;

    /**
     * @var \Magento\Catalog\Ui\Component\ColumnFactory
     */
    protected $columnFactory;

    /**
     * Checkout fields helper
     * @var \Swissup\CheckoutFields\Helper\Data
     */
    protected $helper;

    /**
     * @param CollectionFactory $fieldsCollectionFactory
     * @param \Magento\Catalog\Ui\Component\ColumnFactory $columnFactory
     * @param \Swissup\CheckoutFields\Helper\Data $helper
     */
    public function __construct(
        CollectionFactory $fieldsCollectionFactory,
        \Magento\Catalog\Ui\Component\ColumnFactory $columnFactory,
        \Swissup\CheckoutFields\Helper\Data $helper
    ) {
        $this->fieldsCollectionFactory = $fieldsCollectionFactory;
        $this->columnFactory = $columnFactory;
        $this->helper = $helper;
    }

    /**
     * @param ListingColumns $subject
     */
    public function beforePrepare(ListingColumns $subject)
    {
        if ($this->helper->isEnabled() && $this->isOrderGrid($subject)) {
            $fields = $this->fieldsCollectionFactory->create()
                ->addUsedInGridFilter(1)
                ->addOrder(
                    \Swissup\CheckoutFields\Api\Data\FieldInterface::SORT_ORDER,
                    \Magento\Framework\Data\Collection::SORT_ORDER_ASC
                );

            foreach ($fields as $field) {
                $column = $this->columnFactory->create(
                    $field,
                    $subject->getContext(),
                    ['filter' => false, 'sortable' => false, 'add_field' => false, 'visible' => true]
                );
                $column->prepare();
                $attributeCode = $field->getAttributeCode();
                $subject->addComponent($attributeCode, $column);
            }
        }

        return null;
    }

    /**
     * @param ListingColumns $columns
     * @return bool
     */
    protected function isOrderGrid($columns)
    {
        return $columns->getName() == 'sales_order_columns';
    }
}
