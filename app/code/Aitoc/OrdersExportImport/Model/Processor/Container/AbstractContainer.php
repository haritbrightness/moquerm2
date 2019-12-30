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

use Aitoc\OrdersExportImport\Api\ContainerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class AbstractContainer
 */
abstract class AbstractContainer implements ContainerInterface
{
    /**
     * Line comment prefix
     */
    const COMMENT_PREFIX = '';
    const COMMENT_SUFFIX = '';
    
    /**
     * EOF consts
     */
    const EOL_N = "\n";
    const EOL_R = "\r";

    /**
     * File handle
     */
    protected $handle;

    /**
     * Record data
     */
    protected $record;
    
    /**
     * Record in text form
     */
    protected $recordPlain;
    
    /**
     * Record position
     */
    protected $position;
    
    /**
     * Returns record position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Returns next parsed record
     *
     * @return array|false
     */
    public function currentPlain()
    {
        return $this->recordPlain;
    }
    
    /**
     * Returns next parsed record
     *
     * @return array|false
     */
    public function current()
    {
        return $this->record;
    }

    /**
     * Open by path
     *
     * @param string $path
     * @return void
     */
    public function open($path)
    {
        clearstatcache();
        
        if (!is_file($path)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Unable to open file \'%1\'.', $path)
            );
        }
        
        $this->position = 0;
    }
    
    /**
     * Write file begining
     */
    public function addFileBeginning(array $options = [])
    {
        // add file header
    }
    
    /**
     * Write file ending
     */
    public function addFileEnding(array $options = [])
    {
        // add something to the end of file
    }
    
    /**
     * Move file pointer to specified record number
     *
     * @param int $toPosition
     * @return void
     */
    public function rewind($toPosition)
    {
        $this->position = 0;
        
        while ($this->position != $toPosition) {
            if ($this->readRecord() === false) {
                break;
            }
            
            $this->position++;
        }
    }
    
    /**
     * Returns next parsed record
     *
     * @return array|false
     */
    public function next()
    {
        $this->recordPlain = '';
        $this->record      = [];
        
        $record = $this->readRecord();
        
        if ($record === false) {
            return false;
        }
        
        $this->position++;
        
        $this->recordPlain = $record;
        $this->record      = $this->convert($record);
        
        return $this->record;
    }
}
