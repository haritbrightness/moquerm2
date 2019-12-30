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
 * Class XmlParser
 */
class XmlParser extends AbstractFilter
{
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
        // order data
        
        $found =& $this->findPath($data, '');
        
        $this->normalizeList($found, 'items/item', 'item');
        $this->normalizeList($found, 'addresses/address', 'address');
        $this->normalizeList($found, 'payments/payment', 'payment');
        $this->normalizeList($found, 'statuseshistory/statushistory', 'statushistory');
        
        // sub entities
        
        $this->normalizeList($found, 'shipments/shipment', 'shipment');
        $this->normalizeList($found, 'invoices/invoice', 'invoice');
        $this->normalizeList($found, 'creditmemos/creditmemo', 'creditmemo');
        $this->normalizeList($found, 'paymentstransaction/paymenttransaction', 'paymenttransaction');

        // shipments data
        
        $found =& $this->findPath($data, 'shipment');
        if (empty($found[0])) {
            unset($data['shipment']);
        } else {
            foreach ($found as &$foundEach) {
                $this->normalizeList($foundEach, 'items/item', 'item');
                $this->normalizeList($foundEach, 'comments/comment', 'comment');
                $this->normalizeList($foundEach, 'trackingsinformation/trackinginformation', 'trackinginformation');
                $this->separateFields($foundEach, ['item', 'comment', 'trackinginformation']);
            }
        }
        // invoices data

        $found =& $this->findPath($data, 'invoice');
        if (empty($found[0])) {
            unset($data['invoice']);
        } else {
            foreach ($found as &$foundEach) {
                $this->normalizeList($foundEach, 'items/item', 'item');
                $this->normalizeList($foundEach, 'comments/comment', 'comment');
                $this->separateFields($foundEach, ['item', 'comment']);
            }
        }
        // creditmemos data
        
        $found =& $this->findPath($data, 'creditmemo');
        if (empty($found[0])) {
            unset($data['creditmemo']);
        } else {
            foreach ($found as &$foundEach) {
                $this->normalizeList($foundEach, 'items/item', 'item');
                $this->normalizeList($foundEach, 'comments/comment', 'comment');
                $this->separateFields($foundEach, ['item', 'comment']);
            }
        }
        
        $out = $data;
    }
}
