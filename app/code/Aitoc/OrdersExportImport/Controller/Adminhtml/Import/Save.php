<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Aitoc\OrdersExportImport\Model\Import;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Aitoc\OrdersExportImport\Model\ImportFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::import';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ImportFactory
     */
    private $importFactory;
    
    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ImportFactory $importFactory
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        ImportFactory $importFactory
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->importFactory = $importFactory;
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $data  = $this->getRequest()->getPostValue();
            $model = $this->importFactory->create();

            $data = $this->scopeData($data);
            $model->setData($data);
            $model->save();
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the import.'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected function scopeData($data)
    {
        $newData              = [];
        $newData['import_id'] = null;
        $filename             = '';
        if (!empty($data['file_name'])) {
            foreach ($data['file_name'] as $value) {
                $filename .= $value['path'] . $value['file'];
                if (next($data['file_name'])) {
                    $filename .= ';';
                }
            }
        }
        unset($data['file_name'], $data['form_key']);
        $newData['filename']          = $filename;
        $newData['status']            = 0;
        $newData['serialized_config'] = json_encode($data);
        $newData['dt']                = date('Y-m-d H:i:s');

        return $newData;
    }
}
