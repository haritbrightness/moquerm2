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
namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Aitoc\OrdersExportImport\Block\Adminhtml\Profile\Grid\Renderer\Action\UrlBuilder;
use Aitoc\OrdersExportImport\Model\Import;

/**
 * Class ImportActions
 */
class ImportActions extends Column
{
    /**
     * Url Path
     */
    const URL_DOWNLOAD = 'ordersexportimport/import/download';
    const URL_CRON_IMPORT = 'ordersexportimport/import/manualImport';

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!empty($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['status'] == Import::STATUS_QUEUE) {
                    $url = $this->urlBuilder->getUrl(self::URL_CRON_IMPORT, ['import_id' => $item['import_id']]);
                    $item[$this->getName()] = '<a href="' . $url . '">' . __('Force Run') . '</a>';
                } elseif ($item['status'] == Import::STATUS_COMPLETE) {
                    if ($this->hasErrors($item)) {
                        $url = $this->urlBuilder->getUrl(self::URL_DOWNLOAD, ['id' => $item['import_id']]);
                        $item[$this->getName()] = '<a href="' . $url . '">' . __('Download Error Log') . '</a>';
                    } else {
                        $item[$this->getName()] = __('Successfully Imported');
                    }
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $item
     * @return bool
     */
    private function hasErrors($item)
    {
        $config = empty($item['serialized_config']) ? [] : json_decode($item['serialized_config'], true);
        return !empty($config['profile_result']);
    }
}
