<?php

namespace Swissup\FirecheckoutIntegrations\Observer;

use Magento\Framework\Module\Dir;

class AddLayoutUpdates implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swissup\Firecheckout\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $dirReader;

    /**
     * @param \Swissup\Firecheckout\Helper\Data $helper
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     */
    public function __construct(
        \Swissup\Firecheckout\Helper\Data $helper,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Dir\Reader $dirReader
    ) {
        $this->helper = $helper;
        $this->moduleList = $moduleList;
        $this->dirReader = $dirReader;
    }

    /**
     * Add dynamic handles for all third-party modules
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isOnFirecheckoutPage()) {
            return;
        }

        // collect all existing integration layout files
        $handles = [];
        $path = $this->dirReader
            ->getModuleDir(
                Dir::MODULE_VIEW_DIR,
                'Swissup_FirecheckoutIntegrations'
            )
            . '/frontend/layout';
        $iterator = new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);
        foreach ($iterator as $file) {
            $handle = str_replace('.xml', '', $file->getFilename());
            $handles[$handle] = true;
        }

        // add handles for all active modules only
        $layoutUpdate = $observer->getLayout()->getUpdate();
        foreach ($this->moduleList->getNames() as $moduleName) {
            if (!$this->helper->isModuleOutputEnabled($moduleName)) {
                continue;
            }

            $handle = 'firecheckout_' . strtolower($moduleName);
            if (!isset($handles[$handle])) {
                continue;
            }
            $layoutUpdate->addHandle($handle);
        }
    }
}
