<?php
namespace Swissup\DeliveryDate\Ui\Component\Listing\Columns;

use Swissup\DeliveryDate\Model\DeliverydateFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Date
 * @package Swissup\DeliveryDate\Ui\Component\Listing\Columns
 */
class Date extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Swissup\DeliveryDate\Helper\Data
     */
    protected $helper;

    /**
     * @var DeliverydateFactory
     */
    protected $deliverydateFactory;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\Context $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $timezone
     * @param \Swissup\DeliveryDate\Helper\Data $helper
     * @param DeliverydateFactory $deliverydateFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\Context $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        TimezoneInterface $timezone,
        \Swissup\DeliveryDate\Helper\Data $helper,
        DeliverydateFactory $deliverydateFactory,
        array $components = [],
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->helper = $helper;
        $this->deliverydateFactory = $deliverydateFactory;
        return parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['delivery_date'] = $this->getDeliveryDateByItem($item);
            }
        }
        return $dataSource;
    }

    protected function getDeliveryDateByItem($item)
    {
        $result = 'N/A';
        $deliveryDate = $this->deliverydateFactory
            ->create()
            ->loadByOrderId($item['entity_id']);

        if ($deliveryDate->getId()) {
            $result = [];

            if ($_date = $deliveryDate->getDate()) {
                $configuration = $this->getConfiguration();
                $date = $this->timezone->date(new \DateTime($_date));
                if (empty($configuration['timezone'])) {
                    $date = new \DateTime($_date);
                }
                $dateFormat = isset($configuration['dateFormat']) ? $configuration['dateFormat'] : 'Y-m-d';
                $result[] = $date->format($dateFormat);
            }

            $result[] = $deliveryDate->getTimerange();

            $result = array_filter($result);
            $result = implode(' ', $result);
        }

        return $result;
    }
}
