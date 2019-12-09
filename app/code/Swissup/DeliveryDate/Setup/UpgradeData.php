<?php

namespace Swissup\DeliveryDate\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Swissup\DeliveryDate\Model\ResourceModel\Deliverydate\CollectionFactory
     */
    protected $deliverydateCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $configValueFactory;

    /**
     * @var \Magento\Config\Model\Config\Backend\Serialized\ArraySerializedFactory
     */
    protected $serializedArrayFactory;

    /**
     * @param \Swissup\DeliveryDate\Model\ResourceModel\Deliverydate\CollectionFactory $deliverydateCollectionFactory
     */
    public function __construct(
        \Swissup\DeliveryDate\Model\ResourceModel\Deliverydate\CollectionFactory $deliverydateCollectionFactory,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Config\Model\Config\Backend\Serialized\ArraySerializedFactory $serializedArrayFactory
    ) {
        $this->deliverydateCollectionFactory = $deliverydateCollectionFactory;
        $this->configValueFactory = $configValueFactory;
        $this->serializedArrayFactory = $serializedArrayFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $collection = $this->deliverydateCollectionFactory->create()
                ->addFieldToFilter('date', '0000-00-00 00:00:00');

            foreach ($collection as $item) {
                $item->setDate(null)->save();
            }
        }

        if (version_compare($context->getVersion(), '1.3.1', '<')) {
            $collection = $this->configValueFactory->create()->getCollection()
                ->addFieldToFilter('path', 'delivery_date/exclude/holidays');

            foreach ($collection as $config) {
                if (!$config->getValue()) {
                    continue;
                }

                $raw = @unserialize($config->getValue());
                $model = $this->serializedArrayFactory->create()->setValue($raw);
                $model->beforeSave();

                $config->setValue($model->getValue())->save();
            }
        }

        $setup->endSetup();
    }
}
