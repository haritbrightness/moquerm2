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

use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\File\WriteFactory;
use Aitoc\OrdersExportImport\Model\Processor\Container\CsvMultiline;

/**
 * Class Csv
 */
class Csv extends AbstractContainer
{
    /**
     * Line comment prefix
     */
    const COMMENT_PREFIX = '###';
    
    /**
     *  Csv headers
     */
    protected $headers;

    /**
     * Source file handler
     *
     * @var WriteFactory
     */
    protected $fileFactory;
    
    /**
     * Header flag
     *
     * @var bool
     */
    protected $needAppendHeaderFlag = false;

    /**
     * @var mixed
     */
    protected $recordCache;

    /**
     * Constructor
     */
    public function __construct(WriteFactory $fileFactory)
    {
        $this->fileFactory = $fileFactory;
    }
    
    /**
     * Open by path for read or write
     *
     * @param string $path
     */
    public function open($path)
    {
        $file_parts = pathinfo($path);
        if ($file_parts['extension'] !== 'csv') {
            throw new \Exception('Invalid file format.');
        }
        
        $this->handle = $this->fileFactory->create($path, DriverPool::FILE, 'a+');

        parent::open($path);
        
        $this->handle->seek(0);
        
        $this->readHeader();
    }
    
    /**
     * UTF-8 with BOM fix for csv's from M1 module
     */
    protected function checkBom()
    {
        if (!empty($this->headers)) {
            $this->headers[0] = str_replace(chr(0xEF) . chr(0xBB) . chr(0xBF), '', $this->headers[0]);
        }
    }

    /**
     * Read header
     */
    protected function readHeader()
    {
        $this->headers = $this->handle->readCsv();

        if (!empty($this->headers) && in_array(CsvMultiline::RECORD_TYPE_FIELD, $this->headers)) {
            throw new \Exception('Invalid file format. Possible Multiline CSV file.');
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

        try {
            $pos = $this->handle->tell();
            $this->recordCache = $this->handle->readCsv();
            $length = $this->handle->tell() - $pos;
            $this->handle->seek($pos);

            return $this->handle->read($length);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Convert string data to flat array
     *
     * @param mixed $record
     * @return array|null
     */
    public function convert($record)
    {
        if (is_string($record)) {
            $record = str_getcsv($record);
        } else {
            $record = $this->recordCache;
        }

        // check parsing error
        if (count($this->headers) < count($record)) {
            throw new \Exception('Header field count does not match row field count.');
        }

        $count = min(count($this->headers), count($record));
        
        // assign data to headers
        $record = array_combine(
            array_slice($this->headers, 0, $count), 
            array_slice($record, 0, $count)
        );

        // skip empty fields to avoid data validation errors
        return array_filter($record, function($value) { 
            return $value !== ''; 
        });
    }
    
    /**
     * Write record
     *
     * @param array $record
     */
    public function append(array $record)
    {
        foreach ($record as & $field) {
            $field = str_replace([self::EOL_R . self::EOL_N, self::EOL_N, self::EOL_R], ' ', $field);
        }
        
        if ($this->needAppendHeaderFlag) {
            $this->handle->writeCsv(array_keys($record));
            $this->needAppendHeaderFlag = false;
        }
        
        $this->handle->writeCsv($record);
    }
    
    /**
     * Write data
     *
     * @param mixed $record
     */
    public function writeCommented($content, $message)
    {
        if ($message != null) {
            $content = self::COMMENT_PREFIX
                . $message
                . self::EOL_N
                . $content;
        }
        
        $this->handle->write($content . self::EOL_N);
    }
    
    /**
     * Write file header
     *
     * @param array $headers
     */
    public function addFileBeginning(array $options = [])
    {
        $this->needAppendHeaderFlag = true;
    }
}
