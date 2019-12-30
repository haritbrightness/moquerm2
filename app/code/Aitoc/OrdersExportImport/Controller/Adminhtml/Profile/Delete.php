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
 * Class Delete
 * @package Aitoc\OrdersExportImport\Controller\Adminhtml\Profile
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::profile';
    
    /**
     * @var \Aitoc\OrdersExportImport\Model\ProfileFactory
     */
    private $profileFactory;

    /**
     * Edit constructor.
     *
     * @param Action\Context                             $context
     * @param \Aitoc\OrdersExportImport\Model\ProfileFactory $profileFactory
     */
    public function __construct(
        Action\Context $context,
        \Aitoc\OrdersExportImport\Model\ProfileFactory $profileFactory
    ) {
        parent::__construct($context);
        
        $this->profileFactory = $profileFactory;
    }
    
    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('profile_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->profileFactory->create();
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The profile has been deleted.'));
                // go to grid
                $this->_eventManager->dispatch(
                    'adminhtml_orderexportimport_profile_on_delete',
                    ['title' => $title, 'status' => 'success']
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_orderexportimport_profile_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['page_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a profile to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
