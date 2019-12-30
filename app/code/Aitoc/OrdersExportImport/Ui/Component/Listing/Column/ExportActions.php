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
use Aitoc\OrdersExportImport\Model\Profile;

/**
 * Class ProfileActions
 *
 * @package Aitoc\OrdersExportImport\Ui\Component\Listing\Column
 */
class ExportActions extends Column
{
    const CMS_URL_PATH_DOWNLOAD = 'ordersexportimport/export/download';
    const URL_CRON_EXPORT = 'ordersexportimport/export/manualExport';
    const CMS_URL_PATH_DELETE = 'ordersexportimport/export/delete';

    /*
     * @var UrlInterface 
     */
    private $urlBuilder;
    
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['export_id'])) {
                    if (isset($item['status']) ) {
                        if ($item['status'] == Profile::STATUS_COMPLETE) {
                            $item[$name]['download'] = [
                                'href' => $this->urlBuilder->getUrl(
                                    self::CMS_URL_PATH_DOWNLOAD,
                                    ['id' => $item['export_id']]
                                ),
                                'label' => __('Download')
                            ];
                        } else {
                            $item[$name]['manual'] = [
                                'href' => $this->urlBuilder->getUrl(
                                    self::URL_CRON_EXPORT,
                                    ['export_id' => $item['export_id']]
                                ),
                                'label' => __('Force Run')
                            ];
                        }
                    }

                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::CMS_URL_PATH_DELETE,
                            ['export_id' => $item['export_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.filename }'),
                            'message' => __('Are you sure you wan\'t to delete a ${ $.$data.filename } record?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
