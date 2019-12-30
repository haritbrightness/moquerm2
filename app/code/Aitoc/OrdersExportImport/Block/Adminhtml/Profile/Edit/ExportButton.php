<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Block\Adminhtml\Profile\Edit;

use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\Component\Control\Container;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ExportButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    private $context;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Profile
     */
    private $profile;

    /**
     * ExportButton constructor.
     *
     * @param Context                                 $context
     * @param \Aitoc\OrdersExportImport\Model\Profile $profile
     */
    public function __construct(
        Context $context,
        \Aitoc\OrdersExportImport\Model\Profile $profile
    ) {
        $this->context = $context;
        $this->profile = $profile;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array  $params
     *
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Export All Orders'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'sales_order_grid.sales_order_grid',
                                'actionName' => 'export',
                                'params' => [
                                    false
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
            'sort_order' => 0,
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        $collection = $this->profile->getCollection();
        $optionsCol    = [];

        foreach ($collection as $model) {
            $config = $model->getUnsConfig();
            if (!$config['export_type']) {
                $optionsCol[] = ['label' => $model->getName(), 'value' => $model->getId()];
            }
        }
        foreach ($optionsCol as $optionCode) {
            $options[] = [
                'id_hard' => $this->toCamelCase($optionCode['label']),
                'label' => __($optionCode['label']),
                'onclick' => sprintf(
                    "location.href = '%s';",
                    $this->getUrl('ordersexportimport/profile/export', ['profile_id' => $optionCode['value']])
                ),
            ];
        }
        if (empty($options)) {
            $options[] = [
                'label' => __('No existing profiles')
            ];
        }
        return $options;
    }
}
