<?php

namespace Swissup\Firecheckout\Helper\Config;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Swissup\Firecheckout\Model\Config\Source\BillingAddressDisplayOptions;

class Payment extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_DISPLAY_BILLING_ADDRESS_TITLE = 'firecheckout/payment/display_billing_address_title';

    /**
     * @var string
     */
    const CONFIG_PATH_DISPLAY_BILLING_ADDRESS_ON = 'firecheckout/payment/display_billing_address_on';

    /**
     * @var \Swissup\Firecheckout\Helper\Data $firecheckoutHelper
     */
    private $firecheckoutHelper;

    /**
     * @var \Magento\Checkout\Helper\Data $checkoutHelper
     */
    private $checkoutHelper;

    /**
     * @param Context $context
     * \Swissup\Firecheckout\Helper\Data $firecheckoutHelper
     * \Magento\Checkout\Helper\Data $checkoutHelper
     */
    public function __construct(
        Context $context,
        \Swissup\Firecheckout\Helper\Data $firecheckoutHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper
    ) {
        parent::__construct($context);

        $this->firecheckoutHelper = $firecheckoutHelper;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * @return mixed
     */
    public function getDisplayBillingAddressOn()
    {
        $value = $this->firecheckoutHelper->getConfigValue(
            self::CONFIG_PATH_DISPLAY_BILLING_ADDRESS_ON
        );

        if (!$value || $value === BillingAddressDisplayOptions::OPTION_MAGENTO_CONFIG) {
            $this->firecheckoutHelper->getConfigValue(
                'checkout/options/display_billing_address_on'
            );
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getBillingAddressTitle()
    {
        return $this->firecheckoutHelper->getConfigValue(
            self::CONFIG_PATH_DISPLAY_BILLING_ADDRESS_TITLE
        ) ? __('Billing Address') : '';
    }

    /**
     * @return array
     */
    public function getBillingAddressJsConfig()
    {
        return [
            'title' => $this->getBillingAddressTitle(),
            'position' => $this->getDisplayBillingAddressOn(),
        ];
    }
}
