<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Download
 */
class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::export';

    /**
     * @var \Aitoc\OrdersExportImport\Model\ExportFactory
     */
    protected $exportFactory;
    
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * Constructor
     */
    public function __construct(
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\App\Action\Context $context,
        \Aitoc\OrdersExportImport\Model\ExportFactory $exportFactory
    ) {
        $this->fileFactory   = $fileFactory;
        $this->exportFactory = $exportFactory;
        parent::__construct($context);
    }
    
    /**
     * Execute
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        
        try {
            $model = $this->exportFactory->create();
            $model->load($id);
        
            $file = $model->getFilename();
        
            if (!$file) {
                throw new LocalizedException(__('No file'));
            }
        
            $this->fileFactory->create(
                basename($file),
                [
                    'type' => 'filename',
                    'value' => str_replace(BP, '', $file)
                ],
                DirectoryList::ROOT,
                'application/octet-stream',
                ''
            );
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage('Unable to download this file.');
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/export');
        return $resultRedirect;
    }
}
