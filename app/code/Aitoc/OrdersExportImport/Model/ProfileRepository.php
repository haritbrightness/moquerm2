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
namespace Aitoc\OrdersExportImport\Model;

use Aitoc\OrdersExportImport\Api\Data;
use Aitoc\OrdersExportImport\Api\ProfileRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aitoc\OrdersExportImport\Model\ResourceModel\Profile as ResourceProfile;
use Aitoc\OrdersExportImport\Model\ResourceModel\Profile\CollectionFactory as ProfileCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ProfileRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProfileRepository implements ProfileRepositoryInterface
{
    /**
     * @var ResourceProfile
     */
    protected $resource;

    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var ProfileCollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * @var Data\ProfileSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Aitoc\OrdersExportImport\Api\Data\ProfileInterfaceFactory
     */
    protected $dataProfileFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceProfile $resource
     * @param ProfileFactory $profileFactory
     * @param Data\ProfileInterfaceFactory $dataProfileFactory
     * @param ProfileCollectionFactory $profileCollectionFactory
     * @param Data\ProfileSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceProfile $resource,
        ProfileFactory $profileFactory,
        Data\ProfileInterfaceFactory $dataProfileFactory,
        ProfileCollectionFactory $profileCollectionFactory,
        Data\ProfileSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->profileFactory = $profileFactory;
        $this->profileCollectionFactory = $profileCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataProfileFactory = $dataProfileFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Profile data
     *
     * @param \Aitoc\OrdersExportImport\Api\Data\ProfileInterface $page
     * @return Page
     * @throws CouldNotSaveException
     */
    public function save(\Aitoc\OrdersExportImport\Api\Data\ProfileInterface $profile)
    {
        if (empty($profile->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $profile->setStoreId($storeId);
        }
        try {
            $this->resource->save($profile);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the profile: %1',
                $exception->getMessage()
            ));
        }
        return $profile;
    }

    /**
     * Load Profile data by given Profile Identity
     *
     * @param string $profileId
     * @return Profile
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($profileId)
    {
        $profile = $this->profileFactory->create();
        $profile->load($profileId);
        if (!$profile->getId()) {
            throw new NoSuchEntityException(__('CMS Profile with id "%1" does not exist.', $profileId));
        }
        return $profile;
    }

    /**
     * Load Profile data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Aitoc\OrdersExportImport\Model\ResourceModel\Profile\Collection
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->profileCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $profiles = [];
        /** @var Page $profileModel */
        foreach ($collection as $profileModel) {
            $profileData = $this->dataProfileFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $profileData,
                $profileModel->getData(),
                \Aitoc\OrdersExportImport\Api\Data\ProfileInterface::class
            );
            $profiles[] = $this->dataObjectProcessor->buildOutputDataArray(
                $profileData,
                \Aitoc\OrdersExportImport\Api\Data\ProfileInterface::class
            );
        }
        $searchResults->setItems($profiles);
        return $searchResults;
    }

    /**
     * Delete Profile
     *
     * @param \Aitoc\OrdersExportImport\Api\Data\ProfileInterface $profile
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Aitoc\OrdersExportImport\Api\Data\ProfileInterface $profile)
    {
        try {
            $this->resource->delete($profile);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the profile: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Profile by given Profile Identity
     *
     * @param string $profileId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($profileId)
    {
        return $this->delete($this->getById($profileId));
    }
}
