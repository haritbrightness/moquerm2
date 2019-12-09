<?php

namespace Swissup\Firecheckout\Plugin\Block;

class CartSidebar
{
    /**
     * @var \Swissup\Firecheckout\Helper\Data
     */
    protected $helper;

    /**
     * @param \Swissup\Firecheckout\Helper\Data $helper
     */
    public function __construct(
        \Swissup\Firecheckout\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetCheckoutUrl(
        \Magento\Checkout\Block\Cart\Sidebar $subject,
        \Closure $proceed
    ) {
        if ($this->helper->isFirecheckoutEnabled()) {
            return $this->helper->getFirecheckoutUrl();
        } else {
            return $proceed();
        }
    }
}
