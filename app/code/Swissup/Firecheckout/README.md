# Firecheckout

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/firecheckout:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_Checkout\
    Swissup_Firecheckout

bin/magento setup:upgrade
```
