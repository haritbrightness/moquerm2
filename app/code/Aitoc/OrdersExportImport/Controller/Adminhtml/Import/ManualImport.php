<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */


namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Aitoc\OrdersExportImport\Model\Import;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Aitoc\OrdersExportImport\Model\ImportFactory;

class ManualImport extends \Magento\Backend\App\Action
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
     * @var \Aitoc\OrdersExportImport\Model\ImportProcessorFactory
     */
    private $processorFactory;

    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        \Aitoc\OrdersExportImport\Model\ImportProcessorFactory $processorFactory,
        ImportFactory $importFactory
    )
    {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->importFactory = $importFactory;
        $this->processorFactory = $processorFactory;
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
            $id = $this->getRequest()->getParam('import_id');
            $model = $this->importFactory->create();
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('Profile no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }

                $processor = $this->processorFactory->create();
                $errors = $processor->execute($model);
                if (!$errors) {
                    $this->messageManager->addSuccessMessage(__('Profile was imported!'));
                } else {
                    $this->messageManager->addErrorMessage(__('Profile was imported with errors'));
                    if (count($errors) > 4) {
                        $errors = array_slice($errors, 0, 4);
                        array_push($errors, __('... see more in error log'));
                    }
                    foreach ($errors as $error) {
                        $this->messageManager->addNoticeMessage($error);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
