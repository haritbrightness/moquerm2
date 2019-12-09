# Subscribe at Checkout

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/subscribe-at-checkout:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_Checkout\
    Swissup_SubscribeAtCheckout

bin/magento setup:upgrade
```
