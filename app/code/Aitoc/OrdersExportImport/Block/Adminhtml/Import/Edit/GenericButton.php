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
namespace Aitoc\OrdersExportImport\Block\Adminhtml\Import\Edit;

use Magento\Backend\Block\Widget\Context;
use Aitoc\OrdersExportImport\Api\ProfileRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 *
 * @package Aitoc\OrdersExportImport\Block\Adminhtml\Import\Edit
 */
class GenericButton
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->context = $context;
        $this->request = $request;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * @return bool
     */
    public function canShowButton()
    {
        $isViewOnly = $this->request->getParam('view_only');

        return !$isViewOnly;
    }
}
