<?php

namespace Swissup\Firecheckout\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Swissup\Firecheckout\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Swissup\Firecheckout\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;

    }

    public function getConfig()
    {
        if (!$this->helper->isOnFirecheckoutPage()) {
            return [];
        }

        return [
            'isFirecheckout' => true,
            'swissup' => [
                'firecheckout' => [
                    'layout' => $this->helper->getFirecheckoutLayout(),
                    'shipping' => [
                        'default_method' => $this->helper->getDefaultShippingMethod()
                    ],
                    'payment' => [
                        'default_method' => $this->helper->getDefaultPaymentMethod()
                    ],
                    'dependencies' => [
                        'payment' => $this->getPaymentDependencies()
                    ]
                ]
            ]
        ];
    }

    protected function getPaymentDependencies()
    {
        $result = [];

        if (!$this->checkoutSession->getQuote()->getIsVirtual()) {
            $config = $this->scopeConfig->getValue(
                'payment',
                ScopeInterface::SCOPE_WEBSITE
            );
            foreach ($config as $code => $paymentConfig) {
                if (!empty($paymentConfig['active']) &&
                    !empty($paymentConfig['allowspecific'])) {

                    $result[] = 'address';
                    break;
                }
            }
        }

        return $result;
    }
}
