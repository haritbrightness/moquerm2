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
 * Class Xml
 */
class Xml extends AbstractContainer
{
    /**
     * Line comment prefix
     */
    const COMMENT_PREFIX = '<!--';
    const COMMENT_SUFFIX = '-->';

    /**
     * Data node name
     */
    const RECORD_NODE = 'order';
    
    /**
     * File path
     */
    protected $file;
    
    /**
     * Open by path
     *
     * @param string $path
     * @return void
     */
    public function open($path)
    {
        $this->file = null;
        $file_parts = pathinfo($path);
        if ($file_parts['extension'] !== 'xml') {
            throw new \Exception('Invalid file format.');
        }
        
        $this->file = $path;
        parent::open($path);
        
        $this->handle = new \XMLReader;
        $this->handle->open($path);
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
        
        $preValue = libxml_use_internal_errors(true);
        
        if ($this->handle->name !== self::RECORD_NODE) {
            // search for node skipping parents
            while ($this->handle->read() && $this->handle->name !== self::RECORD_NODE) {
            }
        } else {
            $this->handle->next(self::RECORD_NODE);
        }
        
        libxml_use_internal_errors($preValue);
        
        // next node not found
        if ($this->handle->name !== self::RECORD_NODE) {
            return false;
        }
        
        return $this->handle->readOuterXml();
    }

    /**
     * Convert string data to flat array
     *
     * @param string $string
     * @return array|null
     */
    public function convert($string)
    {
        $xml = simplexml_load_string($string, "SimpleXMLElement", LIBXML_NOCDATA);
        $array = json_decode(json_encode($xml), true);

        return $this->arrayClean($array);
    }
    
    /**
     * Delete empty array branches
     */
    public function arrayClean(&$array)
    {
        foreach ($array as $key => &$v) {
            if (is_array($v)) {
                if (count($v)) {
                    $this->arrayClean($v);
                } 
                if (!count($v)) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }
    
    /**
     * Make xml beauty
     *
     * @param string $xml
     */
    protected function makeBeauty($xml)
    {
        $domxml = new \DOMDocument('1.0');
        
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml);
        
        $result = '';
        
        foreach ($domxml->childNodes as $node) {
            $result .= $domxml->saveXML($node, LIBXML_NOEMPTYTAG);
        }
        
        return $result;
    }
    
    /**
     * Write record
     *
     * @param array $record
     */
    public function append(array $record)
    {
        $xml = new \SimpleXMLElement('<' . self::RECORD_NODE .'/>');
        $this->arrayToXml($record, $xml);

        $content = $this->makeBeauty($xml->asXML());

        file_put_contents($this->file, $content . self::EOL_N, FILE_APPEND);
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
                . self::COMMENT_SUFFIX
                . self::EOL_N
                . $content;
        }
        
        file_put_contents($this->file, $content . self::EOL_N, FILE_APPEND);
    }
    
    /**
     * Write file header
     */
    public function addFileBeginning(array $options = [])
    {
        file_put_contents($this->file, '<?xml version="1.0" encoding="UTF-8"?>' . self::EOL_N);
        file_put_contents($this->file, '<orders>' . self::EOL_N, FILE_APPEND);
    }

    /**
     * Ends file
     */
    public function addFileEnding(array $options = [])
    {
        file_put_contents($this->file, '</orders>', FILE_APPEND);
    }

    /**
     * Adds CData value node
     *
     * @param \SimpleXMLElement &$xmlDocument
     * @param string $value
     */
    protected function addCDataValue($simplexml, $value)
    {
        $node = dom_import_simplexml($simplexml);
        $doc  = $node->ownerDocument;
        $node->appendChild($doc->createCDATASection($value));
    }
    
    /**
     * Recursively convert array to xml document
     *
     * @param array $array
     * @param \SimpleXMLElement &$xmlDocument
     */
    protected function arrayToXml($array, &$xmlDocument)
    {
        $isFirst = true;
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xmlDocument->addChild($key);
                    $this->arrayToXml($value, $subnode);
                } else {
                    if ($isFirst) {
                        $this->arrayToXml($value, $xmlDocument);
                        $isFirst = false;
                    } else {
                        $parent = current($xmlDocument->xpath('parent::*'));
                        $subnode = $parent->addChild($xmlDocument->getName());
                        $this->arrayToXml($value, $subnode);
                    }
                }
            } else {
                $subnode = $xmlDocument->addChild($this->stripXmlChars($key));
                $this->addCDataValue($subnode, $this->stripXmlChars($value));
            }
        }
    }

    /**
     * @param $string
     * @return string
     */
    public function stripXmlChars($string)
    {
        return $string
            ? preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string)
            : $string;
    }
}
