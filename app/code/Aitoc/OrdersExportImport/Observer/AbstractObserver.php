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

use Magento\Framework\Event\ObserverInterface;
use Aitoc\OrdersExportImport\Model\ResourceModel\Profile\CollectionFactory as ProfileCollectionFactory;

/**
 * Class AbstractObserver
 */
abstract class AbstractObserver implements ObserverInterface
{
    /**
     * @var ProfileCollectionFactory
     */
    protected $profileCollectionFactory;
    
    /**
     * Constructor.
     *
     * @param ProfileCollectionFactory $profileCollectionFactory
     */
    public function __construct(
        ProfileCollectionFactory $profileCollectionFactory
    ) {
        $this->profileCollectionFactory = $profileCollectionFactory;
    }
}
