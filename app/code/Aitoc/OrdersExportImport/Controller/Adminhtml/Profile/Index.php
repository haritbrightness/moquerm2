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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Index
 *
 * @package Aitoc\OrdersExportImport\Controller\Adminhtml\Profile
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::profile';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor     = $dataPersistor;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aitoc_OrdersExportImport::export_profiles');
        $resultPage->addBreadcrumb(__('Export Orders'), __('Export Orders'));
        $resultPage->getConfig()->getTitle()->prepend(__('Profile'));

        $this->dataPersistor->clear('aitocexport_profile');

        return $resultPage;
    }
}
