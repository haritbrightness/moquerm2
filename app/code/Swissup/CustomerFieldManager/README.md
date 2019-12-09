# Customer Field Manager

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/customer-field-manager:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_FieldManager\
    Swissup_CustomerFieldManager
bin/magento setup:upgrade
```
