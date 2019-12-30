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
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Validate
 * @package Aitoc\OrdersExportImport\Controller\Adminhtml\Import
 */
class Validate extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::import';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    public $resultRawFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    public $resultFactory;

    /**
     * @var \Aitoc\OrdersExportImport\Model\ImportFactory
     */
    private $importFactory;
    
    /**
     * Constructor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Aitoc\OrdersExportImport\Model\ValidateProcessorFactory $validateProcessorFactory,
        \Aitoc\OrdersExportImport\Model\ImportFactory $importFactory
    ) {
        parent::__construct($context);
        
        $this->resultFactory    = $context->getResultFactory();
        $this->resultRawFactory = $resultRawFactory;
        $this->validateProcessorFactory = $validateProcessorFactory;
        $this->importFactory    = $importFactory;
    }

    /**
     * Download sample file action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        $result    = [];
        
        if ($this->getRequest()->getParam('isAjax')) {
            $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
            $resultBlock  = $resultLayout->getLayout()->getBlock('import.frame.result');
            
            $data         = $this->getRequest()->getPostValue();
            $data         = $this->scopeData($data);
            
            $validator    = $this->validateProcessorFactory->create();
            
            $profile = $this->importFactory->create();
            $profile->setData($data);
            
            try {
                $validator->execute($profile);
                
                $resultBlock->addAction('hide', 'import_validation_container');
                $resultBlock->addSuccess(
                    __('File is valid! To start import process press "Create Import" button'),
                    true
                );
            } catch (\Exception $e) {
                $resultBlock->addAction('show', 'import_validation_container');
                $resultBlock->addError($e->getMessage(), true);
            }

            $result = $resultBlock->getResponseJson();
        }
        
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($result);
        return $resultRaw;
    }

    /**
     * Update data
     *
     * @return array $data
     */
    private function scopeData($data)
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
        $newData['filename']          = $filename;
        $newData['status']            = 0;
        $newData['serialized_config'] = json_encode($data);
        $newData['dt']                = date('Y-m-d H:i:s');

        return $newData;
    }
}
