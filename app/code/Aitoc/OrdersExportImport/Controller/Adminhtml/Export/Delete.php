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

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;

/**
 * Class Delete
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::export';

    /**
     * @var Aitoc\OrdersExportImport\Model\ExportFactory
     */
    protected $exportFactory;
    
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Aitoc\OrdersExportImport\Model\Export $exportFactory
     */
    public function __construct(
        Context $context,
        \Aitoc\OrdersExportImport\Model\ExportFactory $exportFactory
    ) {
        parent::__construct($context);
        $this->exportFactory = $exportFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('export_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->exportFactory->create();
                $model->load($id);
                
                if ($model->getId()) {
                    $file = realpath($model->getFilename());
                    
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                
                $model->delete();
                $this->messageManager->addSuccess(__('The export file has been deleted.'));

                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/index');
            }
        }
        $this->messageManager->addError(__('We can\'t find a export to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
