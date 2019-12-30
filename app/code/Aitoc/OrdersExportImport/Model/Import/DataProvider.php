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

namespace Aitoc\OrdersExportImport\Model\Import;

use Aitoc\OrdersExportImport\Model\ResourceModel\Import\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class DataProvider
 *
 * @package Aitoc\OrdersExportImport\Model\Import
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Url Path
     */
    const URL_DOWNLOAD = 'ordersexportimport/import/download';

    /**
     * @var \itoc\OrdersExportImport\Model\ResourceModel\Import\Collection
     */
    public $collection;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    public $loadedData;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $tmpDirectory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $profileCollectionFactory,
        DataPersistorInterface $dataPersistor,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $profileCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
        $this->tmpDirectory = $filesystem->getDirectoryWrite(DirectoryList::TMP);
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $profile) {
            $data = $profile->getData();

            if (isset($data['config'])) {
                $tempData = json_decode($data['config'], true);
                unset($data['config']);
            } else {
                $tempData = json_decode($data['serialized_config'], true);
                unset($data['serialized_config']);
            }

            $data += $tempData;

            if (!isset($data['file_name']) && isset($data['filename'])) {
                $path = $data['filename'];
                $fileName = basename($path);
                $url = $this->urlBuilder->getUrl(
                    self::URL_DOWNLOAD,
                    [
                        'id' => $data['import_id'],
                        'download_import_file' => 1,
                    ]
                );

                $data['file_name'] = [
                    [
                        'name' => $fileName,
                        'url'  => $url,
                        'size' => $this->getFileSize($path)
                    ]
                ];
            }

            $this->loadedData[$profile->getId()] = $data;
        }

        $data = $this->dataPersistor->get('ordersexportimport_import');
        if (!empty($data)) {
            $profile = $this->collection->getNewEmptyItem();
            $profile->setData($data);
            $this->loadedData[$profile->getId()] = $profile->getData();
            $this->dataPersistor->clear('ordersexportimport_import');
        }
        return $this->loadedData;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getFileSize($path)
    {
        try {
            return $this->tmpDirectory->stat($path)['size'];
        } catch (FileSystemException $exception) {
            return 0;
        }
    }
}
