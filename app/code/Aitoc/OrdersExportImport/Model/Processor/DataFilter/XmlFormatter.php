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
 * Class XmlFormatter
 */
class XmlFormatter extends AbstractFilter
{
    const OPTION_STRUCTURE  = 'structure';
    const NODE_ROOT         = 'root';
    const NODE_MULTIPLE     = 'multiple';
    const NODE_NORMALIZE    = 'normalize';
    const NODE_SOURCE       = 'source';
    const NODE_DESTINATION  = 'destination';
    
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
        $struct = $this->options[self::OPTION_STRUCTURE] ?? [];
        if (empty($struct)) {
            return;
        }

        foreach ($struct as $item) {
            $rootPath   = $item[self::NODE_ROOT] ?? '';
            $children   = $item[self::NODE_NORMALIZE] ?? [];
            $isMultiple = filter_var(
                $item[self::NODE_MULTIPLE] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );
            
            if (!$this->isPath($data, $rootPath)) {
                continue;
            }
            
            $found =& $this->findPath($data, $rootPath);
            
            if ($isMultiple) {
                foreach ($found as &$foundEach) {
                    foreach ($children as $child) {
                        if (isset($child[self::NODE_SOURCE]) && isset($child[self::NODE_DESTINATION])) {
                            $this->normalizeList($foundEach, $child[self::NODE_SOURCE], $child[self::NODE_DESTINATION]);
                        }
                    }
                }
            } else {
                foreach ($children as $child) {
                    if (isset($child[self::NODE_SOURCE]) && isset($child[self::NODE_DESTINATION])) {
                        $this->normalizeList($found, $child[self::NODE_SOURCE], $child[self::NODE_DESTINATION]);
                    }
                }
            }
        }

        $out = $data;
    }
}
