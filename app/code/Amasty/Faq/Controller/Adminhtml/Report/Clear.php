<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Adminhtml\Report;

use Amasty\Faq\Controller\Adminhtml\AbstractReports;
use Magento\Backend\App\Action;
use Amasty\Faq\Model\VisitStatRepository;

class Clear extends AbstractReports
{
    /**
     * @var VisitStatRepository
     */
    private $visitStatRepository;

    public function __construct(
        Action\Context $context,
        VisitStatRepository $visitStatRepository
    ) {
        parent::__construct($context);

        $this->visitStatRepository = $visitStatRepository;
    }

    public function execute()
    {
        $result = $this->visitStatRepository->deleteAll();

        if ($result) {
            $this->messageManager->addSuccessMessage("Grid has been cleared.");
        } else {
            $this->messageManager->addErrorMessage("An error has occured.");
        }

        $this->_redirect->redirect(
            $this->getResponse(),
            'amastyfaq/report/index/'
        );
    }
}
