<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Processor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Aitoc\OrdersExportImport\Api\ContainerInterface;

/**
 * ContainerFactory Class
 */
class ContainerFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * ContainerFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $class
     * @param array $data
     * @return ContainerInterface
     * @throws LocalizedException
     */
    public function create($class, array $data = [])
    {
        if (!class_exists($class)) {
            throw new LocalizedException(
                __('Container \'%1\' is not supported.', $class)
            );
        }
        
        if (!is_subclass_of($class, ContainerInterface::class)) {
            throw new LocalizedException(
                __('Container \'%1\' must implement ContainerInterface.', $class)
            );
        }
        
        return $this->objectManager->create($class, $data);
    }
}
