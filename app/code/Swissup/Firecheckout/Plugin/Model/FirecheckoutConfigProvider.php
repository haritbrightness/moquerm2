<?php

namespace Swissup\Firecheckout\Plugin\Model;

class FirecheckoutConfigProvider
{
    /**
     * @var \Swissup\Firecheckout\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @param \Swissup\Firecheckout\Helper\Data $helper
     */
    public function __construct(
        \Swissup\Firecheckout\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->paymentMethodManagement = $paymentMethodManagement;
    }

    /**
     * Redefine checkoutUrl parameter, and load paymentMethods if they are empty
     *
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     * @return string
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        if ($this->helper->isOnFirecheckoutPage()) {
            if (empty($result['paymentMethods'])) {
                $quote = $this->checkoutSession->getQuote();
                if (!$quote->getIsVirtual()) {
                    foreach ($this->paymentMethodManagement->getList($quote->getId()) as $paymentMethod) {
                        $result['paymentMethods'][] = [
                            'code' => $paymentMethod->getCode(),
                            'title' => $paymentMethod->getTitle()
                        ];
                    }
                }
            }
            $result['checkoutUrl'] = $this->helper->getFirecheckoutUrl();
        }
        return $result;
    }

    /**
     * Retrieve checkout URL
     *
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param string $result
     * @return string
     */
    public function afterGetCheckoutUrl(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        $result
    ) {
        if (!$this->helper->isFirecheckoutEnabled()) {
            return $result;
        }

        if ($this->helper->isOnStandardCheckoutPage()) {
            return $result . '?onepage=1';
        }

        return $this->helper->getFirecheckoutUrl();
    }
}
