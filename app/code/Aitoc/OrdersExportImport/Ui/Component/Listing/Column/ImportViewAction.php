<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */


namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ImportViewAction extends Column
{
    /**
     * Url Path
     */
    const URL_VIEW = 'ordersexportimport/import/edit';

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $url = $this->urlBuilder->getUrl(
                    self::URL_VIEW,
                    [
                        'import_id' => $item['import_id'],
                        'view_only' => 1
                    ]
                );

                $item[$this->getName()] = '<a href="' . $url . '">' . __('View Profile') . '</a>';
            }
        }

        return $dataSource;
    }
}
