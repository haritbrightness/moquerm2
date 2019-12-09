# Order Attachments

Magento Order Attachments module adds ability to attach files to order. It also
provides ability to include attachment links to email template. All attached
files are protected from public access and accessible via private generated
links only.

See more info at our [docs](http://docs.swissuplabs.com/m2/extensions/order-attachments/)

#### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://swissup.github.io/packages/
composer require swissup/orderattachment:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_Checkout\
    Swissup_Orderattachment
bin/magento setup:upgrade
```
