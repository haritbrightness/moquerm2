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

use Magento\Backend\Block\Widget\Context;
use Aitoc\OrdersExportImport\Api\ProfileRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 *
 * @package Aitoc\OrdersExportImport\Block\Adminhtml\Profile\Edit
 */
class GenericButton
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @param Context $context
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        Context $context,
        ProfileRepositoryInterface $profileRepository
    ) {
        $this->context = $context;
        $this->profileRepository = $profileRepository;
    }

    /**
     * Return Profile ID
     *
     * @return int|null
     */
    public function getProfileId()
    {
        try {
            return $this->profileRepository->getById(
                $this->context->getRequest()->getParam('profile_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            $e->getMessage();
        }

        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
