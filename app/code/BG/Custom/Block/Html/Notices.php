<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Frontend form key content block
 */
namespace BG\Custom\Block\Html;

/**
 * @api
 * @since 100.0.2
 */
class Notices extends \Magento\Cookie\Block\Html\Notices
{
    /**
     * Get Link to cookie restriction privacy policy page
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getPrivacyPolicyLink()
    {
        return $this->_urlBuilder->getUrl('privacy-policy');
    }
}
