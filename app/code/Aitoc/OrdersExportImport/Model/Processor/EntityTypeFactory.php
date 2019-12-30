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

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Aitoc\OrdersExportImport\Api\EntityTypeInterface;

/**
 * EntityTypeFactory Class
 */
class EntityTypeFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * ContainerFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $class
     * @param array $data
     * @return EntityTypeInterface
     * @throws LocalizedException
     */
    public function create($class, array $data = [])
    {
        if (!class_exists($class)) {
            throw new LocalizedException(
                __('Entity type \'%1\' is not supported.', $class)
            );
        }
        
        if (!is_subclass_of($class, EntityTypeInterface::class)) {
            throw new LocalizedException(
                __('Entity \'%1\' must implement EntityTypeInterface.', $class)
            );
        }
        
        return $this->objectManager->create($class, $data);
    }
}
