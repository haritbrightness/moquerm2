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
 * Class MultilineCsvParser
 */
class MultilineCsvParser extends AbstractFilter
{
    /**
     * Convert flat array to multi-dim
     *
     * @param array $data
     * @return array
     */
    public function arrayListToMultidimensional($data)
    {
        $structure = [];
        
        // array of last added item pointer for each level
        // ex.: $tail[levelIndex] = & lastItemOfLevelIndex[]
        $tail[] =& $structure;
        
        foreach ($data as $item) {
            $nextType  = $item['entity_type'] ?? '';
            $typeLevel = $this->groupLevel[$nextType]['level'] ?? 1; // default level
            $tag       = $this->groupLevel[$nextType]['tag'] ?? '';
            
            $indexName = $this->groupLevel[$nextType]['rename'] ?? $nextType;
            
            // short variant, pointer to parent(level up) item
            $short =& $tail[$typeLevel-1][$indexName];
            
            // clear non-data field
            unset($item['entity_type']);
            
            // add tag to path
            $short[] = strlen($tag) ? [$tag => $item] : $item;
            
            // update pointer to last parent item of current level
            $tail[$typeLevel] =& $short[count($short)-1];
        }
        
        return $structure;
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
        $this->groupLevel = [
            'order' => ['tag' => 'fields'],
            'invoice' => ['tag' => 'fields'],
            'shipment' => ['tag' => 'fields'],
            'creditmemo' => ['tag' => 'fields'],
            'invoice:comment' => ['level' => 2, 'rename' => 'comment'],
            'invoice:item' => ['level' => 2, 'rename' => 'item'],
            'shipment:comment' => ['level' => 2, 'rename' => 'comment'],
            'shipment:item' => ['level' => 2, 'rename' => 'item'],
            'shipment:trackinginformation' => ['level' => 2, 'rename' => 'trackinginformation'],
            'creditmemo:comment' => ['level' => 2, 'rename' => 'comment'],
            'creditmemo:item' => ['level' => 2, 'rename' => 'item'],
        ];
        
        $data = $this->arrayListToMultidimensional($data);
        
        $this->movePath($data, 'order/0/fields', 'fields');
        
        $out = $data;
    }
}
