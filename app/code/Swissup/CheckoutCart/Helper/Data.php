<?php
namespace Swissup\CheckoutCart\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Path to store config is CheckoutCart enabled
     *
     * @var string
     */
    const MODULE_ENABLED_PATH = 'checkout_cart/general/enabled';

    /**
     * Path to store config show product name as link
     *
     * @var string
     */
    const MODULE_PRODUCT_LINK_ENABLED_PATH = 'checkout_cart/general/product_link_enabled';

    /**
     * Path to store config is change order enabled
     *
     * @var string
     */
    const CHANGE_SORT_ORDER_PATH = 'checkout_cart/general/change_sort_order';

    protected function _getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if module is enabled in admin
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)$this->_getConfig(self::MODULE_ENABLED_PATH);
    }

    /**
     * Check if product name should be rendered as link
     * @return boolean
     */
    public function productLinkEnabled()
    {
        return (bool)$this->_getConfig(self::MODULE_PRODUCT_LINK_ENABLED_PATH);
    }

    /**
     * Check if change order is enabled in admin
     * @return boolean
     */
    public function isChangeOrderEnabled()
    {
        return (bool)$this->_getConfig(self::CHANGE_SORT_ORDER_PATH);
    }
}
