<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Import;

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
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::import';

    /**
     * @var \Aitoc\OrdersExportImport\Model\ImportFactory
     */
    protected $importFactory;
    
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
        \Aitoc\OrdersExportImport\Model\ImportFactory $importFactory
    ) {
        $this->fileFactory           = $fileFactory;
        $this->importFactory         = $importFactory;
        parent::__construct($context);
    }
    
    /**
     * Execute
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        
        try {
            $model = $this->importFactory->create();
            $model->load($id);
        
            $file = $model->getConfig()->getProfileResult();

            if ($this->getRequest()->getParam('download_import_file')) {
                $file = $model->getFilename();
            }
        
            if (!$file) {
                throw new LocalizedException(__('No file'));
            }
            
            $this->fileFactory->create(
                basename($file),
                [
                    'type' => 'filename',
                    'value' => $file
                ],
                DirectoryList::VAR_DIR,
                'application/octet-stream',
                ''
            );
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage('Unable to download this file.');
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/import');
            return $resultRedirect;
        }

    }
}
