# Address Field Manager

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/address-field-manager:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_FieldManager\
    Swissup_AddressFieldManager
bin/magento setup:upgrade
```
