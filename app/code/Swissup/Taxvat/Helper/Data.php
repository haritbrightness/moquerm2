<?php
namespace Swissup\Taxvat\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\RemoteServiceUnavailableException;

class Data extends AbstractHelper
{
    /**
     * Path to store config is VAT field enabled
     *
     * @var string
     */
    const VAT_ENABLED = 'customer/create_account/vat_frontend_visibility';
    /**
     * Path to store config is VIES validation enabled
     *
     * @var string
     */
    const VALIDATION_ENABLED = 'taxvat/general/validate';
    /**
     * Path to store config
     *
     * @var string
     */
    const ALLOW_INVALID = 'taxvat/general/allow_invalid';
    /**
     * Path to store config is VAT field required
     *
     * @var string
     */
    const VAT_FIELD_REQUIRED = 'taxvat/general/required';
    /**
     * @var string
     */
    const TAX_FREE_ENABLED = 'taxvat/general/tax_free';
    /**
     * @var string
     */
    const TAX_FREE_ALL = 'taxvat/general/tax_free_all';
    /**
     * @var string
     */
    const TAX_FREE_COUNTRIES = 'taxvat/general/tax_free_countries';
    /**
     * @var \Magento\Customer\Model\Vat
     */
    protected $customerVat;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Vat $customerVat
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Vat $customerVat
    ) {
        $this->customerVat = $customerVat;
        parent::__construct($context);
    }

    protected function _getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if VAT field is enabled in admin
     * @return boolean
     */
    public function isVatFieldEnabled()
    {
        return (bool)$this->_getConfig(self::VAT_ENABLED);
    }

    /**
     * Check if VAT validation is enabled in admin
     * @return boolean
     */
    public function isValidationEnabled()
    {
        return (bool)$this->_getConfig(self::VALIDATION_ENABLED);
    }

    /**
     * Check if VAT field is required
     * @return boolean
     */
    public function isVatRequired()
    {
        return (bool)$this->_getConfig(self::VAT_FIELD_REQUIRED);
    }

    /**
     * Check if both VAT field and validation are enabled
     * @return bool
     */
    public function canValidateVat()
    {
        return self::isVatFieldEnabled() && self::isValidationEnabled();
    }

    /**
     * Check if both VAT field and validation are enabled
     * @return bool
     */
    public function allowInvalidVat()
    {
        return (bool)$this->_getConfig(self::ALLOW_INVALID);
    }

    /**
     * Check if tax should be removed for valid VAT numbers
     *
     * @param string $countryCode
     * @return bool
     */
    public function isTaxFreeEnabled($countryCode = null)
    {
        $isEnabled = (bool)$this->_getConfig(self::TAX_FREE_ENABLED);
        if (!$countryCode || !$isEnabled) {
            return $isEnabled;
        }

        $isEnabledForCountry = (bool)$this->_getConfig(self::TAX_FREE_ALL);
        if (!$isEnabledForCountry) {
            $allowedCountries = $this->_getConfig(self::TAX_FREE_COUNTRIES);
            if ($allowedCountries) {
                $allowedCountries = explode(',', $allowedCountries);
                $isEnabledForCountry = in_array($countryCode, $allowedCountries);
            }
        }

        return $isEnabledForCountry;
    }

    /**
     * @param mixed $needles
     * @return boolean
     */
    public function hasInUrl($needles)
    {
        if (!is_array($needles)) {
            $needles = [$needles];
        }

        $pathInfo = $this->_request->getPathInfo();
        foreach ($needles as $needle) {
            if (strpos($pathInfo, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isPost()
    {
        return $this->_request->isPost();
    }

    /**
     * Validate VAT number using VIES service
     *
     * @param  string $countryCode
     * @param  string $vatNumber
     * @return bool
     * @throws RemoteServiceUnavailableException
     */
    public function validateVat($countryCode, $vatNumber)
    {
        if (!$countryCode || !$vatNumber) {
            return false;
        }

        $result = $this->customerVat->checkVatNumber($countryCode, $vatNumber);
        if (!$result->getRequestSuccess()) {
            throw new RemoteServiceUnavailableException(
                __("VAT validation service is not available")
            );
        }

        return $result->getIsValid();
    }
}
