<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller\Price;

/**
 * Class Index
 */
class Index extends \Amasty\Xnotif\Controller\AbstractIndex
{
    const TYPE = "price";

    public function getTitle()
    {
        return __("My Price Subscriptions");
    }
}
