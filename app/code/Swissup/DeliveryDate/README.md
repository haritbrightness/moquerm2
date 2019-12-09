# Delivery Date

### Description

The Magento 2 module Delivery Date gives your customers possibility to choose the date on which they want the products purchased from your store to be delivered.

### Installation

```bash
cd <magento_root>
composer config repositories.swissup/delivery-date vcs git@github.com:swissup/delivery-date.git
composer require swissup/delivery-date:dev-master --prefer-source
bin/magento module:enable Swissup_DeliveryDate Swissup_Checkout
bin/magento setup:upgrade
bin/magento setup:di:compile
```
