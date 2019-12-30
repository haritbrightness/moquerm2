<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Block\Adminhtml\Import\Frame;

use Magento\Framework\View\Element\Template;

/**
 * Import frame result block.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Result extends \Magento\ImportExport\Block\Adminhtml\Import\Frame\Result
{
    /**
     * Validation messages.
     *
     * @var array
     */
    protected $messages = ['error' => [], 'success' => [], 'notice' => [], 'deleted' => []];

    protected $actions = [
            'clear' => [],
            'innerHTML' => [],
            'value' => [],
            'show' => [],
            'hide' => [],
            'removeClassName' => [],
            'addClassName' => [],
            'exception' => []
        ];

    /**
     * Add delete message.
     *
     * @param string[]|string $message Message text
     * @param bool $appendImportButton OPTIONAL Append import button to message?
     * @return $this
     */
    public function addDeleted($message, $appendImportButton = false)
    {
        if (is_array($message)) {
            foreach ($message as $row) {
                $this->addDeleted($row);
            }
        } else {
            $this->messages['deleted'][] = $message . ($appendImportButton ? $this->getImportButtonHtml() : '');
        }
        return $this;
    }
    
    /**
     * @return int
     */
    public function getErrors()
    {
        return count($this->messages['error'] + $this->messages['notice'] + $this->messages['success']);
    }

    /**
     * @return string
     */
    public function getImportButtonHtml()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getMessages()
    {
        $text = '';
        $imported = count($this->messages['success']);
        $notice = count($this->messages['notice']);
        $errors = count($this->messages['error']);
        $deleted = count($this->messages['deleted']);
        if ($deleted) {
            $text = '<br/>Deleted - ' . $deleted . '; Failed - ' . $notice . '; Errors  - ' . $errors . ' ';
        } else {
            $text = '<br/>Imported - ' . $imported . '; Failed - ' . $notice . '; Errors  - ' . $errors . ' ';
        }
        $text .= "<br/>";
        foreach ($this->messages as $priority => $messages) {
            if (!in_array($priority, ['success', 'deleted'])) {
                foreach ($messages as $message) {
                    $text .= $message . "<br/>";
                }
            }
        }

        return $text;
    }
}
