<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Processor\Container;

/**
 * Class Csvmultiline
 */
class CsvMultiline extends Csv
{
    const RECORD_TYPE_FIELD = 'entity_type';
    const RECORD_TYPE_VALUE = 'order';
    
    /**
     * Read header
     */
    protected function readHeader()
    {
        $this->headers = $this->handle->readCsv();
        if (!empty($this->headers) && !in_array(CsvMultiline::RECORD_TYPE_FIELD, $this->headers)) {
            if (count($this->headers) !== 1 || strpos($this->headers[0], '###') !== 0) { //check if error file
                throw new \Exception('Entity type field \'' . self::RECORD_TYPE_FIELD . '\' is missing. Possible Simple CSV file.');
            }
        }

        $this->checkBom();
    }
    
    /**
     * Search and returns next record
     *
     * @return bool|string
     */
    public function readRecord()
    {
        if (!$this->handle) {
            return false;
        }
        
        $lines = [];
        $lastPosition = $this->handle->tell();
        while ($line = parent::readRecord()) {
            // record end ?
            if (count($lines) && $this->isNewRecord($line)) {
                $this->handle->seek($lastPosition);
                break;
            }
            $lastPosition = $this->handle->tell();
            $lines[] = $line;
        }
        
        // false or lines
        return count($lines) ? implode(self::EOL_N, $lines) : false;
    }
    
    /**
     * Recognize new record begining by line
     *
     * @param string $line
     * @return bool
     */
    protected function isNewRecord($line)
    {
        $data = parent::convert($line);
        $type = $data[self::RECORD_TYPE_FIELD] ?? '';
        
        return $type == self::RECORD_TYPE_VALUE;
    }

    /**
     * Convert string data to flat array
     *
     * @param string $string
     * @return array|null
     */
    public function convert($string)
    {
        $record = [];
        $lines  = explode(self::EOL_N . self::EOL_N, $string) ?? [];
        $lines = array_filter($lines);

        foreach ($lines as $line) {
            $record[] = array_filter(
                parent::convert($line),
                function ($item) {
                    return ($item !== '');
                }
            );
        }

        return $record;
    }
    
    /**
     * Write Multiline record
     *
     * @param array $record
     */
    public function append(array $record)
    {
        foreach ($record as $single) {
            parent::append($single);
        }
    }
}
