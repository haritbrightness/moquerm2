<?php

namespace BG\Shipping\Block;

class Shipping extends \Magento\Framework\View\Element\Template {

    protected $_isScopePrivate;
    protected $country;

    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Directory\Model\Country $country,
            \MagePal\GeoIp\Service\GeoIpService $geoIpService,
            \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
            array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->geoIpService = $geoIpService;
        $this->remoteAddress = $remoteAddress;
        $this->country = $country;
    }

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getCountries() {
        $countries = $this->country->getCollection()->toOptionArray();
        $currentCountry = $this->geoIpService();
        $options = "";
        foreach ($countries as $country) {
            if ($country['value'] != "") {
                $selected = "";
                if($currentCountry==$country['value']){
                    $selected = ' selected="selected"';
                }
                $options .= '<option value="' . $country['value'] . '"'.$selected.'>' . $country['label'] . '</option>';
            }
        }
        return $options;
    }

    public function getFormAction() {
        return $this->getUrl('custom-shipping', ['_secure' => true]);
    }

    public function getNumbers() {
        $options = "";
        $_number = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1500, 2000];
        foreach ($_number as $_num) {
            $options .= '<option value="' . $_num . '">' . $_num . '</option>';
        }
        return $options;
    }

    public function geoIpService() {
        return $this->geoIpService->getCountryByIpAddress($this->remoteAddress->getRemoteAddress());
    }
    
    public function getCacheLifetime()
    {
        return null;
    }


}
