<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SocialLogin
 */


namespace Amasty\SocialLogin\Controller\Forgot;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;

/**
 * Class ForgotPasswordPost
 */
class ForgotPasswordPost extends \Magento\Customer\Controller\Account\ForgotPasswordPost
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        Escaper $escaper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context, $customerSession, $customerAccountManagement, $escaper);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return string
     */
    public function execute()
    {
        parent::execute();
        $resultJson = $this->resultJsonFactory->create();
        $success = $this->messageManager->getMessages()->getItemsByType('success');
        $error = $this->messageManager->getMessages()->getErrors();
        $this->messageManager->getMessages(true);

        return $resultJson->setData(
            [
                'error' => $this->getFirstMessage($error),
                'success' => $this->getFirstMessage($success)
            ]
        );
    }

    /**
     * @param array $messages
     * @return string
     */
    private function getFirstMessage($messages)
    {
        return isset($messages[0]) ? $messages[0]->getText() : '';
    }
}
