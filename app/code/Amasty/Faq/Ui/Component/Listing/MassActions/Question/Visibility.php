<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Ui\Component\Listing\MassActions\Question;

class Visibility extends \Amasty\Faq\Ui\Component\Listing\MassActions\MassAction
{
    /**
     * {@inheritdoc}
     */
    public function getUrlParams($optionValue)
    {
        return ['visibility' => $optionValue];
    }
}
