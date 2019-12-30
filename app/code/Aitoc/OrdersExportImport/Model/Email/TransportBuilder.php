<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Email;

/**
 * Class TransportBuilder
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @param $filePath
     * @param $filename
     */
    public function createAttachment($filePath, $filename)
    {
        $fileContents = file_get_contents($filePath);
        $this->message->createAttachment($fileContents. 'text/csv')->filename = $filename;

        return $this;
    }
}
