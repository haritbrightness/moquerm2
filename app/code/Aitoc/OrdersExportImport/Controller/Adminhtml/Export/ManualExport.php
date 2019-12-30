<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */


namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Aitoc\OrdersExportImport\Model\ExportFactory;
use Aitoc\OrdersExportImport\Model\Profile;

class ManualExport extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::export';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ExportFactory
     */
    private $exportFactory;

    /**
     * @var \Aitoc\OrdersExportImport\Model\ImportProcessorFactory
     */
    private $processorFactory;

    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        \Aitoc\OrdersExportImport\Model\ExportProcessorFactory $processorFactory,
        ExportFactory $exportFactory
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->exportFactory = $exportFactory;
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
            $id = $this->getRequest()->getParam('export_id');
            /** @var \Aitoc\OrdersExportImport\Model\Export $model */
            $model = $this->exportFactory->create();

            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This profile no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }

                $processor = $this->processorFactory->create();
                while ($model->getStatus() != Profile::STATUS_COMPLETE) {
                    $processor->execute($model);
                }
                $this->messageManager->addSuccessMessage(__('Profile was exported!'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while export profile.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
