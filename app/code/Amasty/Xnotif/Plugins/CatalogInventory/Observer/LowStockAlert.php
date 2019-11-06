<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Plugins\CatalogInventory\Observer;

use Amasty\Xnotif\Helper\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Observer\ItemsForReindex;
use Magento\CatalogInventory\Observer\SubtractQuoteInventoryObserver;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class LowStockAlert
 */
class LowStockAlert
{
    const XML_PATH_LOW_STOCK_CONFIG = 'admin_notifications/low_stock_alert';

    const XML_PATH_EMAIL_TO = 'admin_notifications/stock_alert_email';

    const XML_PATH_SENDER_EMAIL = 'admin_notifications/sender_email_identity';

    const TEMPLATE_FILE = 'Amasty_Xnotif::notifications/low_stock_alert.phtml';

    /**
     * @var ItemsForReindex
     */
    private $itemsForReindex;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Layout
     */
    private $layout;

    public function __construct(
        ItemsForReindex $itemsForReindex,
        Config $config,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger,
        Layout $layout
    ) {
        $this->itemsForReindex = $itemsForReindex;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->layout = $layout;
    }

    /**
     * @param SubtractQuoteInventoryObserver $subject
     * @param SubtractQuoteInventoryObserver $result
     */
    public function afterExecute($subject, $result)
    {
        $emailTo = $this->getEmailTo();
        $sender = $this->config->getModuleConfig(self::XML_PATH_SENDER_EMAIL);

        if ($this->config->getModuleConfig(self::XML_PATH_LOW_STOCK_CONFIG) && $emailTo && $sender) {
            $storeId = $this->storeManager->getStore()->getId();
            $products = $this->getLowStockItems($storeId);

            if (empty($products)) {
                return;
            }

            try {
                $lowStockHtml = $this->getLowStockHtml($products);

                if ($lowStockHtml) {
                    $transport = $this->transportBuilder->setTemplateIdentifier(
                        $this->config->getModuleConfig('admin_notifications/notify_low_stock_template')
                    )->setTemplateOptions(
                        ['area' => Area::AREA_FRONTEND, 'store' => $storeId]
                    )->setTemplateVars(
                        ['alertGrid' => $lowStockHtml]
                    )->setFrom(
                        $sender
                    )->addTo(
                        $emailTo
                    )->getTransport();
                    $transport->sendMessage();
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

    /**
     * @return array|mixed
     */
    protected function getEmailTo()
    {
        $emailTo = $this->config->getModuleConfig(self::XML_PATH_EMAIL_TO);

        if (strpos($emailTo, ',') !== false) {
            $emailTo = explode(',', $emailTo);
        }

        return $emailTo;
    }

    /**
     * @param array $products
     *
     * @return string
     */
    protected function getLowStockHtml($products)
    {
        /** @var Template $lowStockAlert */
        $lowStockAlert = $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE_FILE)
            ->setData('lowStockProducts', $products);

        return trim($lowStockAlert->toHtml());
    }

    /**
     * @param int $storeId
     *
     * @return array
     */
    protected function getLowStockItems($storeId)
    {
        $products = [];

        foreach ($this->getCollectionItems() as $lowStockItem) {
            if (!$storeId) {
                $storeId = $lowStockItem->getStoreId();
            }

            $product = $this->initProduct($lowStockItem->getProductId(), $storeId);
            $products[] = [
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'qty' => $lowStockItem->getQty()
            ];
        }

        return $products;
    }

    /**
     * @return array
     */
    protected function getCollectionItems()
    {
        return $this->itemsForReindex->getItems();
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    protected function initProduct($productId, $storeId)
    {
        return $this->productRepository->getById(
            $productId,
            false,
            $storeId
        );
    }
}
