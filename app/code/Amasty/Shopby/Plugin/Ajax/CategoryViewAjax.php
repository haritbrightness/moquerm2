<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\Page;

/**
 * Class CategoryViewAjax
 * @package Amasty\Shopby\Plugin\Ajax
 */
class CategoryViewAjax extends Ajax
{
    /**
     * @param Action $controller
     *
     * @return array
     */
    public function beforeExecute(Action $controller)
    {
        if ($this->isAjax($controller->getRequest())) {
            $this->getActionFlag()->set('', 'no-renderLayout', false);
        }

        return [];
    }

    /**
     * @param Action $controller
     * @param Page $page
     *
     * @return \Magento\Framework\Controller\Result\Raw|Page
     */
    public function afterExecute(Action $controller, $page)
    {  
        return $page;
    }
}
