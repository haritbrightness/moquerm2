<?php
namespace Fooman\PdfCustomiser\Model\System;

/**
 * Display all configured designs as dropdown choices
 *
 * @author     Kristof Ringleff
 * @package    Fooman_PdfCustomiser
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class DesignOptions implements \Magento\Framework\Data\OptionSourceInterface
{

    private $pdfDesignConfig;

    public function __construct(
        \Fooman\PdfCustomiser\Model\Config\PdfDesignData $designData
    ) {
        $this->pdfDesignConfig = $designData;
    }

    public function toOptionArray()
    {
        return $this->pdfDesignConfig->getPdfDesignOptions();
    }
}
