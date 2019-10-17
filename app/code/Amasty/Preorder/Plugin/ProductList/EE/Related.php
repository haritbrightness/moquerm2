<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Preorder
 */


namespace Amasty\Preorder\Plugin\ProductList\EE;

class Related extends \Amasty\Preorder\Plugin\AbstractProductList
{
    /**
     * @param \Magento\TargetRule\Block\Catalog\Product\ProductList\Related $subject
     * @param string $resultHtml
     *
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        $this->setProductCollection($subject->getItemCollection());

        return parent::afterToHtml($subject, $resultHtml);
    }
}
