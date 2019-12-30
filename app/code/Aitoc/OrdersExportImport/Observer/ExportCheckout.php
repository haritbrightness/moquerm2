<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Observer;

use Aitoc\OrdersExportImport\Model\Profile;

/**
 * Class ExportCheckout
 */
class ExportCheckout extends AbstractObserver
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
        
        if ($order->getId()) {
            $profileCollection = $this->profileCollectionFactory->create();
            
            // collect profile ids for checkout export
            foreach ($profileCollection as $profile) {
                if ($profile->getConfig()->getEntity() == 'order' &&
                    $profile->getConfig()->getExportType() == Profile::TYPE_CHECKOUT
                ) {
                    $profile->updateTasksWithData($order->getId());
                }
            }
        }
        
        return $this;
    }
}
