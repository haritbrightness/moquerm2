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
 * Class ColumnPath
 */
class ColumnPath extends AbstractFilter
{
    /**
     * Array of options with defaults
     */
    protected $options = [
        'fieldname_parse_pattern' => '',
    ];
    
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
        if (empty($this->options['fieldname_parse_pattern'])) {
            return;
        }
        
        $result = [];
        
        foreach ($data as $columnName => $value) {
            if (preg_match_all($this->options['fieldname_parse_pattern'], $columnName, $part)) {
                
                if (count($part[0]) == 1) {
                    $extra = ['fields'];
                    $extra[] = array_pop($part[0]);
                    $part[0] = array_merge($part[0], $extra);
                }
            
                if (count($part[0]) == 3 && in_array($part[0][0], ['invoice', 'shipment', 'creditmemo'])) {
                    $extra = ['fields'];
                    $extra[] = array_pop($part[0]);
                    $part[0] = array_merge($part[0], $extra);
                }
                
                $this->setPathValue($result, implode('/', $part[0]), $value);
            }
        }
        
        $out = $result;
    }
}
