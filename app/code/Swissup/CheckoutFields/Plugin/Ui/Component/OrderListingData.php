<?php
namespace Swissup\CheckoutFields\Plugin\Ui\Component;

use Swissup\CheckoutFields\Model\ResourceModel\Field\Collection;

class OrderListingData
{
    /**
     * Checkout fields collection
     * @var Collection
     */
    protected $fieldsCollection;

    /**
     * Checkout fields helper
     * @var \Swissup\CheckoutFields\Helper\Data
     */
    protected $helper;

    /**
     * @param Collection $fieldsCollection
     * @param \Swissup\CheckoutFields\Helper\Data $helper
     */
    public function __construct(
        Collection $fieldsCollection,
        \Swissup\CheckoutFields\Helper\Data $helper
    ) {
        $this->fieldsCollection = $fieldsCollection;
        $this->helper = $helper;
    }

    /**
     * @param  \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $subject
     * @param  \Magento\Framework\Api\Search\SearchResultInterface $result
     * @return \Magento\Framework\Api\Search\SearchResultInterface
     */
    public function afterSearch(
        \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $subject,
        \Magento\Framework\Api\Search\SearchResultInterface $result
    ) {
        $connection = $result->getConnection();
        if ($this->helper->isEnabled() &&
            $result->getMainTable() === $connection->getTableName('sales_order_grid')
        ) {
            $select = $result->getSelect();
            $select->joinLeft(
                ['swissupcfv' => $connection->getTableName('swissup_checkoutfields_values')],
                'main_table.entity_id = swissupcfv.order_id AND main_table.store_id = swissupcfv.store_id',
                []
            )->joinLeft(
                ['swissupcf' => $connection->getTableName('swissup_checkoutfields_field')],
                'swissupcf.field_id = swissupcfv.field_id',
                []
            )->group('main_table.entity_id')
            ->columns($this->getFieldsColumns());

            // Fix column in where clause is ambiguous error
            $where = $select->getPart('where');
            foreach ($where as &$item) {
                if (strpos($item, '(`store_id`') !== false) {
                    $item = str_replace('`store_id`', '`main_table`.`store_id`', $item);
                }

                if (strpos($item, '(`created_at`') !== false) {
                    $item = str_replace('`created_at`', '`main_table`.`created_at`', $item);
                }
            }
            $select->setPart('where', $where);
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getFieldsColumns()
    {
        $columns = [];
        $fields = $this->fieldsCollection->addUsedInGridFilter(1);
        $codes = $fields->getColumnValues('attribute_code');
        foreach ($codes as $code) {
            $columns[$code] = new \Zend_Db_Expr("MAX(CASE WHEN (swissupcf.attribute_code = '$code') THEN swissupcfv.value ELSE NULL END)");
        }

        return $columns;
    }
}
