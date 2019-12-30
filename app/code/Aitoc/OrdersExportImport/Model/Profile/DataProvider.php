<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Profile;

use Aitoc\OrdersExportImport\Model\ResourceModel\Profile\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 *
 * @package Aitoc\OrdersExportImport\Model\Profile
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Aitoc\OrdersExportImport\Model\ResourceModel\Profile\Collection
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
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $profileCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $profileCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $profileCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
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
            $tempData = json_decode($data['config'], true);
            unset($data['config']);
            $data += $tempData;
            $this->loadedData[$profile->getId()] = $data;
        }

        $data = $this->dataPersistor->get('ordersexportimport_profile');
        if (!empty($data)) {
            $profile = $this->collection->getNewEmptyItem();
            $profile->setData($data);
            $this->loadedData[$profile->getId()] = $profile->getData();
            $this->dataPersistor->clear('ordersexportimport_profile');
        }

        return $this->loadedData;
    }
}
