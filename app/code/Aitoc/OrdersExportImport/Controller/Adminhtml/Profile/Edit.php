<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Aitoc\OrdersExportImport\Controller\Adminhtml\Profile
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::profile';

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
     * @var \Aitoc\OrdersExportImport\Model\ProfileFactory
     */
    private $profileFactory;

    /**
     * Edit constructor.
     *
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Aitoc\OrdersExportImport\Model\ProfileFactory $profileFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Aitoc\OrdersExportImport\Model\ProfileFactory $profileFactory
    ) {
        parent::__construct($context);
        
        $this->profileFactory    = $profileFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry      = $registry;
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
        $resultPage->setActiveMenu('Aitoc_OrdersExportImport::export_profiles');
        $resultPage->addBreadcrumb(__('Export Orders'), __('Export Orders'));
        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('profile_id');
        $model = $this->profileFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This profile no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('orderexportimport_profile', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Profile') : __('New Profile'),
            $id ? __('Edit Profile') : __('New Profile')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Profiles'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Profile'));

        return $resultPage;
    }
}
