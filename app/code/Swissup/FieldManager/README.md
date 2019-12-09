# Field Manager

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/field-manager:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_FieldManager
bin/magento setup:upgrade
```
