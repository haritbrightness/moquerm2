# Swissup Checkout

Dummy checkout module. It's purpose is to add swissup menu and config sections.

### Installation

```bash
cd <magento_root>
composer config repositories.swissup/checkout vcs git@github.com:swissup/checkout.git
composer require swissup/module-checkout --prefer-source
bin/magento module:enable Swissup_Checkout
bin/magento setup:upgrade
```
