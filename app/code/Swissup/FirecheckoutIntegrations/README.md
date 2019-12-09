# Firecheckout integrations

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/firecheckout-integrations:dev-master --prefer-source
bin/magento module:enable Swissup_FirecheckoutIntegrations
bin/magento setup:upgrade
```
