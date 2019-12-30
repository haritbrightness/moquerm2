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
use Aitoc\OrdersExportImport\Model\Profile;
use Magento\Framework\Exception\LocalizedException;

class Export extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::profile';

    const URL_HISTORY    = 'ordersexportimport/export/index';
    
    /**
     * @var ProfileFactory
     */
    protected $profileFactory;
    
    /**
     * Constructor
     *
     * @param Context $context
     * @param ProfileFactory $profileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aitoc\OrdersExportImport\Model\ProfileFactory $profileFactory
    ) {
        parent::__construct($context);
        $this->profileFactory = $profileFactory;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($this->getRequest()->getParam('profile_id')) {
            $profile = $this->profileFactory->create()
                ->load($this->getRequest()->getParam('profile_id'));
            
            try {
                if (!$profile->getId()) {
                    throw new LocalizedException(__('This profile no longer exists.'));
                }
                
                $task = $profile->createTask($profile);
                $task->setConfig($this->getRequest()->getParams());
                $task->save();
                
                $this->messageManager->addSuccess(
                    __(
                        'You begin to export the profile. You can see the export status <a href="%1">here</a>.',
                        $this->_backendUrl->getUrl(self::URL_HISTORY)
                    )
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while exporting the profile.'));
            }
        }

        return $resultRedirect->setPath('sales/order');
    }
}
