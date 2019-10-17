<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Stat;

use Amasty\Faq\Model\VisitStatFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Amasty\Faq\Api\VisitStatRepositoryInterface;
use Magento\Customer\Model\Visitor;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;

class Collect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    /**
     * @var VisitStatRepositoryInterface
     */
    private $visitStatRepository;

    /**
     * @var VisitStatFactory
     */
    private $visitStatFactory;

    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Collect constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param VisitStatRepositoryInterface $visitStatRepository
     * @param VisitStatFactory $visitStatFactory
     * @param Visitor $visitor
     * @param CustomerSession $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Page\Config $pageConfig,
        VisitStatRepositoryInterface $visitStatRepository,
        VisitStatFactory $visitStatFactory,
        Visitor $visitor,
        CustomerSession $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->pageConfig = $pageConfig;
        $this->visitStatRepository = $visitStatRepository;
        $this->visitStatFactory = $visitStatFactory;
        $this->visitor = $visitor;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (empty($params['search_query'])) {
            return;
        }
        $visitStat = $this->visitStatFactory->create();
        /** @var \Amasty\Faq\Model\VisitStat $visitStat */
        $visitStat->addData($params);
        if ($this->customerSession->getCustomerId()) {
            $visitStat->setCustomerId($this->customerSession->getCustomerId());
        } else {
            $visitStat->setVisitorId($this->visitor->getId());
        }
        $visitStat->setStoreId($this->storeManager->getStore()->getId());

        try {
            $this->visitStatRepository->save($visitStat);
        } catch (LocalizedException $e) {
            // do nothing
        }
    }
}
