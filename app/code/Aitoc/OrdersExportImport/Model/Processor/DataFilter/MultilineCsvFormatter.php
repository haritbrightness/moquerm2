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
 * Class MultilineCsvFormatter
 */
class MultilineCsvFormatter extends AbstractFilter
{
    /**
     * Cached headers
     */
    protected $headers;
    
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
        if (empty($this->options['data_scheme'])) {
            return;
        }
        
        if (!$this->headers) {
            $this->headers = ['entity_type' => null] + $this->compactHeaders($this->options['data_scheme']);
        }
        
        $replaceType = [
            'fields' => 'order',
            'invoice:fields' => 'invoice',
            'shipment:fields' => 'shipment',
            'creditmemo:fields' => 'creditmemo',
        ];
        
        $data = $this->traverseStructure($data);
        
        foreach ($data as &$item) {
            if (isset($replaceType[$item['entity_type']])) {
                $item['entity_type'] = $replaceType[$item['entity_type']];
            }
            
            $item = array_merge($this->headers, $item);
        }
        
        $out = $data;
    }

    /**
     * Generate list of unique field names (dublicates remove)
     *
     * @param array $data
     */
    protected function compactHeaders(array $struct)
    {
        $fieldList = [];
        
        foreach ($struct as $value) {
            if (is_array($value)) {
                $fieldList += $this->compactHeaders($value);
            } else {
                $fieldList[$value] = null;
            }
        }
        
        return $fieldList;
    }
    
    /**
     * iterate
     *
     * @param array $data
     */
    public function traverseStructure(array $data, $pathPrefix = '', $type = '')
    {
        $out   = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $newPathPrefix = trim($pathPrefix . ':' . $key, ':');
                $newType       = trim($type . (is_numeric($key) ? '' : ':' . $key), ':');

                $out+= $this->traverseStructure($value, $newPathPrefix, $newType);
            } else {
                if (!isset($out[$pathPrefix]['entity_type'])) {
                    $out[$pathPrefix]['entity_type'] = $type;
                }
                
                $out[$pathPrefix][$key] = $value;
            }
        }
        return $out;
    }
}
