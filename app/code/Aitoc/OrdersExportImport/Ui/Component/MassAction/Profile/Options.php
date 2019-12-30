<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Ui\Component\MassAction\Profile;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;

/**
 * Class Options
 * @package Aitoc\OrdersExportImport\Ui\Component\MassAction\Profile
 */
class Options implements \Zend\Stdlib\JsonSerializable
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    protected $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * @var \Aitoc\OrdersExportImport\Model\Profile
     */
    private $profile;

    /**
     * Constructor
     *
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Aitoc\OrdersExportImport\Model\Profile $profile,
        array $data = []
    ) {
        $this->data       = $data;
        $this->urlBuilder = $urlBuilder;
        $this->profile    = $profile;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->options = [];
        $collection = $this->profile->getCollection();
        $options    = [];

        foreach ($collection as $model) {
            $config = $model->getUnsConfig();
            if (!$config['export_type']) {
                $options[] = ['label' => $model->getName(), 'value' => $model->getId()];
            }
        }
        if (!count($options)) {
            $options[] = [
                'label' => __('No existing profiles'),
                'value' => '',
                'click' => 'return false;'
            ];
        }
        $this->prepareData();
        foreach ($options as $optionCode) {
            $this->options[$optionCode['value']] = [
                'type' => 'customer_group_' . $optionCode['value'],
                'label' => $optionCode['label'],
            ];
            if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
            }

            $this->options[$optionCode['value']] = array_merge_recursive(
                $this->options[$optionCode['value']],
                $this->additionalData
            );
        }

        $this->options = array_values($this->options);
        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
