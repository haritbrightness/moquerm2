<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Processor\DataFilter;

/**
 * Class AbstractFilter
 */
abstract class AbstractFilter implements \Aitoc\OrdersExportImport\Api\DataFilterInterface
{
    /**
     * Array of options with defaults
     */
    protected $options = [];
    
    /**
     * Add filter options
     *
     * @param array $options
     */
    public function addOptions($options)
    {
        if (is_array($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }
    
    /**
     * @param array $array
     * @param string $path
     */
    public function isPath($array, $path)
    {
        if ($path == '' || $path == '/') {
            return true;
        }
        $path = explode('/', $path);
        foreach ($path as $key) {
            if (!is_array($array)) {
                return false;
            }
            if (!array_key_exists($key, $array)) {
                return false;
            }
            $array = &$array[$key];
        }
        return true;
    }
    
    /**
     * Returns pointer to item by path
     * Don't touch if you ain't prayed %)
     *
     * @param array $array
     * @param string $path
     */
    public function &findPath(&$array, $path)
    {
        $path  = strlen($path) ? explode('/', $path) : [];
        $refer = &$array;
        
        foreach ($path as $key) {
            if (!array_key_exists($key, $refer)) {
                $refer[$key] = [];
            }
            $refer = &$refer[$key];
        }
        
        return $refer;
    }
    
    /**
     * @param array $array
     * @param string $path
     */
    public function getPathValue($array, $path)
    {
        $path = explode('/', $path);
        foreach ($path as $key) {
            if (!array_key_exists($key, $array)) {
                return;
            }
            $array = &$array[$key];
        }
        return $array;
    }
    
    /**
     * @param array $array
     * @param string $path
     * @param mixed $value
     */
    public function setPathValue(&$array, $path, $value)
    {
        $path = explode('/', $path);
        foreach ($path as $key) {
            $array = &$array[$key];
        }
        $array = $value;
    }
    
    /**
     * @param array $data
     * @param string $path
     */
    public function isPathFinal($data, $path)
    {
        // check like !isset( $arr['some']['array']['path'][0] )
        return ($this->isPath($data, $path) && !$this->isPath($data, $path . '/0'));
    }
    
    /**
     * @param array $array
     * @param string $source
     * @param string $destination
     */
    public function moveFields(&$array, $source, $destination)
    {
        $value = $this->getPathValue($array, $source);
        
        $parts = explode('/', $source);
        if (isset($parts[0])) {
            unset($array[$parts[0]]);
        }
        
        $this->setPathValue($array, $destination, $value);
    }
    
    /**
     * @param array $array
     * @param string $source
     * @param string $destination
     */
    public function movePath(&$array, $source, $destination)
    {
        $value = $this->getPathValue($array, $source);
        
        $parts = explode('/', $source);
        if (isset($parts[0])) {
            unset($array[$parts[0]]);
        }
        
        $this->setPathValue($array, $destination, $value);
    }
    
    /**
     * @param array $data
     * @param string $path
     * @param string $newPath
     */
    public function normalizeList(&$data, $path, $newPath)
    {
        // reduce unnecessary tag
        if ($this->isPath($data, $path)) {
            // no sub items as normal list with num keys
            if (!$this->isPath($data, $path . '/0')) {
                $newPath .= '/0';
            }
            
            $this->movePath($data, $path, $newPath);
        }
    }

    /**
     * @param array $data
     * @param array $exclude
     * @param string $tagName
     */
    public function separateFields(&$data, $exclude = [], $tagName = 'fields')
    {
        if (!is_array($data)) {
            return;
        }
        $exclude[] = $tagName;
        $saved     = [$tagName => []];

        foreach ($exclude as $key) {
            if (array_key_exists($key, $data)) {
                $saved[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        $saved[$tagName] = array_merge($saved[$tagName], $data);
        $data = $saved;
    }

    /**
     * Transforms data by rules
     *
     * @param array $data
     * @return mixed
     */
    abstract public function execute($data, &$out);
}
