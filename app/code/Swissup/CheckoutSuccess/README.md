# Checkout Success Page

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/checkout-success:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_Checkout\
    Swissup_CheckoutSuccess
bin/magento setup:upgrade
```
