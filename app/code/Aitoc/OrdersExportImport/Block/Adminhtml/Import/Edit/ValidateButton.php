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

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 *
 * @package Aitoc\OrdersExportImport\Block\Adminhtml\Import\Edit
 */
class ValidateButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->canShowButton()) {
            return [];
        }

        return [
            'label' => __('Check Data'),
            'class' => 'primary save',
            'on_click' =>'aitocImport.postToFrame();',
            'sort_order' => 20,
        ];
    }
}
