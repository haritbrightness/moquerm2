# Checkout Cart

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/module-checkout-cart:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_Checkout\
    Swissup_CheckoutCart
bin/magento setup:upgrade
```
