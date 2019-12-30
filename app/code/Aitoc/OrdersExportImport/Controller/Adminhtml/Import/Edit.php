<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 *
 * Copyright © Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Aitoc\OrdersExportImport\Controller\Adminhtml\Import
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::import';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Aitoc\OrdersExportImport\Model\ImportFactory
     */
    private $importFactory;

    /**
     * Edit constructor.
     *
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Aitoc\OrdersExportImport\Model\ImportFactory $importFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Aitoc\OrdersExportImport\Model\ImportFactory $importFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry      = $registry;
        $this->importFactory     = $importFactory;
    }
        
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aitoc_OrdersExportImport::import_edit');
        $resultPage->addBreadcrumb(__('Import file'), __('Import file'));
        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id    = $this->getRequest()->getParam('import_id');
        $model = $this->importFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This profile no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('orderexportimport_import', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Import') : __('Import file'),
            $id ? __('Import') : __('Import file')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('Import file'));

        return $resultPage;
    }
}
