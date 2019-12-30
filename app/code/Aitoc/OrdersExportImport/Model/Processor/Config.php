<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Processor;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
class Config extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var object
     */
    protected $profile;
    
    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($data);
        $this->scopeConfig = $scopeConfig;
        $this->reinitData();
    }
    
    /**
     * Init data
     *
     * @return $this
     */
    public function reinitData()
    {
        $this->setData(
            $this->getModuleConfig('aitoie/general') // admin configs
        )->addData(
            $this->getModuleConfig('processor') // config.xml
        );
        
        return $this;
    }
    
    /**
     * Retrieve module configs
     *
     * @param string $path
     * @param string $scope
     * @return mixed
     */
    public function getModuleConfig($path, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue($path, $scope);
    }
}
