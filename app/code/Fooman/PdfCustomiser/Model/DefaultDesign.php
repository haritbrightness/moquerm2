<?php

namespace Fooman\PdfCustomiser\Model;

/**
 * Design source for Default Pdf Design
 *
 * @author     Kristof Ringleff
 * @package    Fooman_PdfCustomiser
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class DefaultDesign implements Api\DesignInterface
{
    private $defaultTemplateFiles = [
        'logo' => 'Fooman_PdfCore::pdf/logo.phtml',
        'table' => 'Fooman_PdfCore::pdf/table.phtml',
        'shipping' => 'Fooman_PdfCustomiser::pdf/shipping.phtml',
        'comments' => 'Fooman_PdfCustomiser::pdf/comments.phtml',
        'giftmessage' => 'Fooman_PdfCustomiser::pdf/giftmessage.phtml',
        'taxTable' => 'Fooman_PdfCustomiser::pdf/taxtable.phtml',
        'totals' => 'Fooman_PdfCustomiser::pdf/totals.phtml',
        'bundleExtras' => 'Fooman_PdfCustomiser::pdf/table/bundle-extras.phtml',
        'extras' => 'Fooman_PdfCustomiser::pdf/table/extras.phtml',
        'paymentDefault' => 'Fooman_PdfCustomiser::pdf/payment-default.phtml'
    ];

    private $storeId;

    public function getStoreId()
    {
        return $this->storeId;
    }

    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    public function getLayoutHandle($pdfType)
    {
        return sprintf('fooman_pdfcustomiser_%s', $pdfType);
    }

    /**
     * @return array
     */
    public function getItemStyling()
    {
        return [
            'header' => [
                'default' => 'border-bottom:1px solid black;',
                'first' => 'border-bottom:1px solid black;',
                'last' => 'border-bottom:1px solid black;'
            ],
            'row' => [
                'default' => 'border-bottom:0px none transparent;',
                'last' => 'border-bottom:0px solid black;',
                'first' => 'border-bottom:0px none transparent;'
            ],
            'table' => ['default' => 'padding: 2px 0px;']
        ];
    }

    public function getTemplateFiles()
    {
        return $this->defaultTemplateFiles;
    }

    public function getFooterLayoutHandle()
    {
        return \Fooman\PdfCore\Block\Pdf\DocumentRenderer::DEFAULT_FOOTER_LAYOUT_HANDLE;
    }
}
