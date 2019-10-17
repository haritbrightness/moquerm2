<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Adminhtml\Report;

use Magento\Framework\Controller\ResultFactory;
use Amasty\Faq\Controller\Adminhtml\AbstractReports;

class Index extends AbstractReports
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Faq::report');
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Search Terms Report'));

        return $resultPage;
    }
}
