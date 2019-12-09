<?php
namespace Swissup\CheckoutFields\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $formElementMap = [
        'text' => 'Magento_Ui/js/form/element/abstract',
        'textarea' => 'Magento_Ui/js/form/element/textarea',
        'select' => 'Magento_Ui/js/form/element/select',
        'boolean' => 'Magento_Ui/js/form/element/select',
        'multiselect' => 'Magento_Ui/js/form/element/checkbox-set',
        'date' => 'Magento_Ui/js/form/element/date'
    ];

    protected $templateMap = [
        'text' => 'ui/form/element/input',
        'textarea' => 'ui/form/element/textarea',
        'select' => 'ui/form/element/select',
        'boolean' => 'ui/form/element/select',
        'multiselect' => 'ui/form/element/checkbox-set',
        'date' => 'ui/form/element/date'
    ];

    /**
     * Path to store config checkout fields enabled
     *
     * @var string
     */
    const XML_PATH_ENABLED = 'checkoutfields/general/enabled';

    /**
     * Get config value by key
     * @param  string $key config path
     * @return string
     */
    protected function _getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Module is enabled config
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)$this->_getConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Get field Ui component by frontend type
     * @param \Swissup\CheckoutFields\Model\Field $field
     * @return string
     */
    public function getFieldUiComponent($field)
    {
        return $this->formElementMap[$field->getFrontendInput()];
    }

    /**
     * Get field template by frontend type
     * @param \Swissup\CheckoutFields\Model\Field $field
     * @return string
     */
    public function getFieldTemplate($field)
    {
        return $this->templateMap[$field->getFrontendInput()];
    }
}
