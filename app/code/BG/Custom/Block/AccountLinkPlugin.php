<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


namespace BG\Custom\Block;

use Amasty\Gdpr\Model\Config;
use Magento\Customer\Model\Session;

class AccountLinkPlugin extends \Amasty\Gdpr\Block\AccountLinkPlugin
{
    const SORT_ORDER = 175;
    const INSERT_AFTER = 'customer-account-navigation-account-edit-link';

    const M21_LINK_CLASS = 'Magento\Framework\View\Element\Html\Link\Current';
    const M22_LINK_CLASS = 'Magento\Customer\Block\Account\SortLinkInterface';

    const LINK_BLOCK_NAME = 'customer-account-amasty-gdpr-settings';
    const LINK_BLOCK_ALIAS = 'amasty-gdpr-link';

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * AccountLinkPlugin constructor.
     *
     * @param Config $configProvider
     */
    public function __construct(
        Config $configProvider,
        Session $customerSession
    ) {
        parent::__construct($configProvider,$customerSession);
    }

    /**
     * Insert menu item depending on Magento version
     *
     * @param \Magento\Framework\View\Element\Html\Links|\Magento\Customer\Block\Account\Navigation $subject
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeGetLinks($subject)
    {
       
    }
}
