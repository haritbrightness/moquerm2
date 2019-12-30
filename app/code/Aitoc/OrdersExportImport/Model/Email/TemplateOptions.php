<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Email;

/**
 * Class TemplateOptions
 */
class TemplateOptions extends \Magento\Framework\DataObject
    implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @var \Magento\Email\Model\Template\Config
     */
    private $emailConfig;

    /**
     * @var \Magento\Email\Model\ResourceModel\Template\CollectionFactory
     */
    public $templatesFactory;

    /**
     * Template constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        array $data = []
    ) {
        parent::__construct($data);
        $this->coreRegistry = $coreRegistry;
        $this->templatesFactory = $templatesFactory;
        $this->emailConfig = $emailConfig;
    }

    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $collection \Magento\Email\Model\ResourceModel\Template\Collection */
        if (!($collection = $this->coreRegistry->registry('config_system_email_template'))) {
            $collection = $this->templatesFactory->create();
            $collection->load();
            $this->coreRegistry->register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateId = str_replace('/', '_', 'oei/email/template');
        if ($templateId) {
            $templateLabel = $this->emailConfig->getTemplateLabel($templateId);
            $templateLabel = __('%1 (Default)', $templateLabel);
            array_unshift($options, ['value' => $templateId, 'label' => $templateLabel]);
        }

        return $options;
    }
}
