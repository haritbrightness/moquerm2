<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface ProfileRepositoryInterface
 *
 * @package Aitoc\OrdersExportImport\Api
 */
interface ProfileRepositoryInterface
{
    /**
     * Save profile.
     *
     * @param \Aitoc\OrdersExportImport\Api\Data\ProfileInterface $block
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\ProfileInterface $profile);

    /**
     * Retrieve profile.
     *
     * @param int $profileId
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($profileId);

    /**
     * Retrieve profiles matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete profile.
     *
     * @param \Aitoc\OrdersExportImport\Api\Data\ProfileInterface $profile
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ProfileInterface $profile);

    /**
     * Delete profile by ID.
     *
     * @param int $profileId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($profileId);
}
