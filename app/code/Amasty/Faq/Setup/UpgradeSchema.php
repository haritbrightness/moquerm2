<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\UpgradeTo110
     */
    private $upgradeTo110;

    /**
     * @var Operation\UpgradeTo200
     */
    private $upgradeTo200;

    /**
     * @param SchemaSetupInterface   $setup
     * @var Operation\CreateViewStatTables
     */
    private $createStatTable;

    /**
     * @var Operation\AddTriggers
     */
    private $triggers;

    /**
     * @var Operation\UpgradeTo234
     */
    private $upgradeTo234;

    /**
     * @var Operation\UpgradeTo240
     */
    private $upgradeTo240;

    /**
     * @var Operation\AddIndex
     */
    private $addIndex;

    public function __construct(
        Operation\UpgradeTo110 $upgradeTo110,
        Operation\UpgradeTo200 $upgradeTo200,
        Operation\CreateViewStatTables $createStatTable,
        Operation\AddTriggers $triggers,
        Operation\UpgradeTo234 $upgradeTo234,
        Operation\UpgradeTo240 $upgradeTo240,
        Operation\AddIndex $addIndex
    ) {
        $this->upgradeTo110 = $upgradeTo110;
        $this->upgradeTo200 = $upgradeTo200;
        $this->createStatTable = $createStatTable;
        $this->triggers = $triggers;
        $this->upgradeTo234 = $upgradeTo234;
        $this->upgradeTo240 = $upgradeTo240;
        $this->addIndex = $addIndex;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->createStatTable->execute($setup);
            $this->upgradeTo110->execute($setup);
            $this->triggers->addVisitStatTrigger($setup);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->upgradeTo200->execute($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '2.3.4', '<')) {
            $this->upgradeTo234->execute($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '2.4.0', '<')) {
            $this->upgradeTo240->execute($setup);
        }
        if (version_compare($context->getVersion(), '2.5.0', '<')) {
            $this->addIndex->execute($setup);
        }

        $setup->endSetup();
    }
}
