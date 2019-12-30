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

use Aitoc\OrdersExportImport\Api\DataFilterInterface;

/**
 * Converter Class
 */
class Converter
{
    /**
     * @var \Aitoc\OrdersExportImport\Model\Processor\FilterFactory
     */
    protected $filterFactory;
    
    /**
     * Array of available filters
     *
     * @var array
     */
    protected $filters = [];
    
    /**
     * Data array
     *
     * @var array
     */
    protected $data = [];
    
    /**
     * ContainerFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\Processor\FilterFactory $filterFactory
    ) {
        $this->filterFactory = $filterFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Returns filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
    
    /**
     * Add new filter to converter
     *
     * @param string $filterClass
     */
    public function addFilter($code, $filterClass)
    {
        if (!is_subclass_of($filterClass, DataFilterInterface::class)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Filter \'%1\' must implement DataFilterInterface', $filterClass)
            );
        }
        
        $filter = $this->filterFactory->create($filterClass);

        if (!isset($this->filters[$code])) {
            $this->filters[$code] = $filter;
        }
    }
    
    /**
     * Add filter options
     *
     * @param array $options
     */
    public function addFilterOptions($options)
    {
        if (!is_array($options)) {
            return;
        }
        
        $globalOptions = [];

        foreach ($options as $code => $option) {
            $option = is_array($option) ? $option : [];
            if (isset($this->filters[$code])) {
                $this->filters[$code]->addOptions($option);
            } else {
                $globalOptions = array_merge($globalOptions, $option);
            }
        }
        
        // add global options for all filters
        if (count($globalOptions)) {
            foreach ($this->filters as $filter) {
                $filter->addOptions($globalOptions);
            }
        }
    }
    
    /**
     * Modify imported data
     *
     * @param array $fieldData
     */
    protected function updateData()
    {
        $filters = $this->getFilters();

        if (count($filters)) {
            $modifiedItem = $this->data;

            foreach ($filters as $code => $filter) {
                $filter->execute($modifiedItem, $modifiedItem);
            }

            $this->data = $modifiedItem;
        }
    }
    
    /**
     * Converts data
     *
     * @param array &$data
     * @return array
     */
    public function apply($data)
    {
        $this->data = $data;
        
        // data filters should be applied to final struct
        $this->updateData();
        
        return $this->data;
    }
}
