<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Aitoc\OrdersExportImport\Model\ImportFactory;

/**
 * Class Index
 */
class Index extends \Magento\Backend\App\Action
{
    const BYTES_IN_MB = 1024 * 1024;
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::manage';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ImportFactory
     */
    private $importFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ImportFactory $importFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory
    ) {
        parent::__construct($context);
        
        $this->resultPageFactory = $resultPageFactory;
        $this->importFactory     = $importFactory;
        $this->scopeConfig       = $scopeConfig;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $limit = $this->scopeConfig->getValue(
            'aitoie/general/errorlog_diskspace_limit',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        $import = $this->importFactory->create();
        
        if ($import->collectTotalFilesize() > $limit * self::BYTES_IN_MB) {
            $this->messageManager->addWarning(
                __('Total size of import error log files exceeds configured limit.')
            );
        }
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aitoc_OrdersExportImport::import');
        $resultPage->addBreadcrumb(__('Import History'), __('Import History'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import History'));

        return $resultPage;
    }
}
