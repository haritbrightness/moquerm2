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
 * Class SimpleCsvParser
 */
class SimpleCsvParser extends AbstractFilter
{
    const ANY_PART = '*';
    
    /**
     * Detect that realPath contains pathPart from start
     */
    public function isPathMatch($realPath, $pathPart)
    {
        if (!is_array($realPath) || !is_array($pathPart)) {
            return  false;
        }
        
        for ($i=0; $i<count($pathPart); $i++) {
            // realPath is shorter than pathPart
            if (!isset($realPath[$i])) {
                return false;
            // sub items of same level is not equal
            } elseif ($pathPart[$i] !== $realPath[$i] && $pathPart[$i] != self::ANY_PART) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check equality
     */
    public function isPathEqual($realPath, $pathPart)
    {
        if (!is_array($realPath) || !is_array($pathPart)) {
            return  false;
        }
        
        if (count($realPath) == count($pathPart) &&
            $this->isPathMatch($realPath, $pathPart)
        ) {
            return true;
        }
        
        return  false;
    }

    /**
     *
     */
    public function replacePath($path, $src, $dst)
    {
        $path = array_values($path);
        
        $newPath = [];
        for ($i=0; $i<count($dst); $i++) {
            if ($dst[$i] == self::ANY_PART && isset($path[$i])) {
                $newPath[] = $path[$i];
            }
            
            if ($dst[$i] != self::ANY_PART || !isset($path[$i])) {
                $newPath[] = $dst[$i];
            }
        }
        
        // modify path with dst
        for ($i=count($src); $i<count($path); $i++) {
            $newPath[] = $path[$i];
        }
    
        return $newPath;
    }
    
    /**
     * Transforms data by rules
     *
     * @param array $data [
     *      array  'path'
     *      string 'field'
     *      string 'value'
     * ]
     */
    public function execute($data, &$out)
    {
        if (empty($this->options)) {
            return;
        }
        
        $optionToAction = [
            'equal' => 'isPathEqual',
            'like'  => 'isPathMatch',
        ];
        
        foreach ($this->options as $option) {
            if (array_key_exists('replace', $option)) {
                // replace string to array
                $to = empty($option['replace']) ? [] : explode('/', $option['replace']);
                
                foreach ($optionToAction as $code => $action) {
                    if (array_key_exists($code, $option)) {
                        $from = empty($option[$code]) ? [] : explode('/', $option[$code]);
                        
                        if ($this->$action($data['path'], $from)) {
                            $out['path'] = $this->replacePath($data['path'], $from, $to);
                            $data = $out;
                        }
                    }
                }
            }
        }
    }
}
