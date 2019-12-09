# Address Autocomplete

Magento 2 module for address autocompleting. See docs at
[docs.swissuplabs.com](http://docs.swissuplabs.com/m2/extensions/address-autocomplete/).

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/module-address-autocomplete:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_Checkout\
    Swissup_AddressAutocomplete
bin/magento setup:upgrade

rm -rf pub/static/_requirejs var/view_preprocessed
bin/magento setup:static-content:deploy
```
