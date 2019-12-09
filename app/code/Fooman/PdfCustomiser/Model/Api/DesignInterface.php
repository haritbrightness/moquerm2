<?php

namespace Fooman\PdfCustomiser\Model\Api;

interface DesignInterface
{
    public function getLayoutHandle($pdfType);

    public function getItemStyling();

    public function getTemplateFiles();

    public function getFooterLayoutHandle();

    public function getStoreId();
    public function setStoreId($storeId);
}
