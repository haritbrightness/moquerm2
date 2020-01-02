<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * require __DIR__ . '/app/bootstrap.php';
 * $params = $_SERVER;
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'website2';
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
 * $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
 * \/** @var \Magento\Framework\App\Http $app *\/
 * $app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
 * $bootstrap->run($app);
 * --------------------------------------------
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

try {
    require __DIR__ . '/app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    exit(1);
}

$params = $_SERVER;

switch($_SERVER['HTTP_HOST']) {

        case 'moquer.nl':
        case 'www.moquer.nl':
             $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'nl';
                         $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'store';
        break;


        case 'moquer.fr':
        case 'www.moquer.fr':
             $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'fr';
                         $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'store';
        break;


        case 'moquer.de':
        case 'www.moquer.de':
             $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'de';
                         $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'store';
        break;


        case 'moquer.com':
        case 'www.moquer.com':
             $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'en';
                         $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'store';
        break;


        case 'moquer.be':
        case 'www.moquer.be':
             $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'be';
                         $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'store';
        break;
}

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);
