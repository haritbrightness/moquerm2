<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model;

use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Store\Model\StoreManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Aitoc\OrdersExportImport\Model\Profile;
use Aitoc\OrdersExportImport\Model\Email\TransportBuilder;
use Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapperFactory;
use Aitoc\OrdersExportImport\Model\Processor\ConfigFactory;
use Aitoc\OrdersExportImport\Model\Processor\ConverterFactory;
use Aitoc\OrdersExportImport\Model\Processor\ContainerFactory;
use Aitoc\OrdersExportImport\Model\Processor\EntityTypeFactory;

/**
 * Class ExportProcessor
 */
class ExportProcessor extends AbstractProcessor
{
    /**
     * @var ExportConfigMapperFactory
     */
    protected $configMapperFactory;
    
    /**
     * @var Ftp
     */
    protected $ftpConnect;
    
    /**
     * @var StoreManager
     */
    public $store;

    /**
     * @var ScopeConfigInterface
     */
    protected $scope;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Aitoc\Core\Model\Logger
     */
    private $logger;

    public function __construct(
        ConfigFactory $configFactory,
        ConverterFactory $converterFactory,
        ContainerFactory $containerFactory,
        EntityTypeFactory $entityTypeFactory,
        ExportConfigMapperFactory $configMapperFactory,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scope,
        StoreManager $store,
        Ftp $ioFtp,
        \Aitoc\Core\Model\Logger $logger
    ) {
        parent::__construct(
            $configFactory,
            $converterFactory,
            $containerFactory,
            $entityTypeFactory
        );

        $this->configMapperFactory = $configMapperFactory;
        $this->ftpConnect          = $ioFtp;
        $this->transportBuilder    = $transportBuilder;
        $this->scope               = $scope;
        $this->store               = $store;
        $this->logger = $logger;
    }
    
    /**
     * Init and start processing
     *
     * @param $export
     */
    public function execute($export)
    {
        $mapper = $this->configMapperFactory->create();
        $config = $mapper->toConfig($export);
        
        $this->export = $export;
        
        parent::execute($config);
        
        $this->export();
    }
    
    /**
     * Init and start processing
     */
    public function prepareConditions()
    {
        $lastId   = $this->export->getConfig()->getLastExportedId() * 1;
        $selected = $this->export->getConfig()->getSelected();
        
        $conditionList = [['entity_id' => ['gt' => $lastId]]];
        
        if ($selected) {
            $conditionList[] = ['entity_id' => ['in' => $selected]];
        }
        
        return $conditionList;
    }
    
    /**
     * Init and start processing
     */
    public function prepareScheme()
    {
        $allowed = $this->export->getProfile()->getConfig()->getAllowedFields();
        $invert  = ['invoice', 'creditmemo', 'shipment'];
        
        foreach ($invert as $index) {
            if (isset($allowed[$index]) && !empty($allowed[$index])) {
                $allowed[$index] = array_fill_keys($allowed[$index], []);
            }
        }
        
        if (isset($allowed['order']) && !empty($allowed['order'])) {
            $allowed = array_merge($allowed, array_fill_keys($allowed['order'], []));
            unset($allowed['order']);
        }
        
        return $allowed;
    }
    
    /**
     * Export
     */
    public function export()
    {
        $this->getEntity()->setDataScheme($this->prepareScheme());
        $schemeOption = ['data_scheme' => $this->getEntity()->getDataScheme()];
        $this->converter->addFilterOptions([$schemeOption]);
        // add filters from profile
        $this->converter->addFilterOptions(
            $this->export->getProfile()->getConfig()->getFilters()
        );

        if (!$this->export->getConfig()->getLastExportedId()) {
            $this->container->addFileBeginning();
        }

        $data = $this->getEntity()->retrieve($this->prepareConditions());
        
        foreach ($data as $item) {
            if (!empty($item['fields']['entity_id'])) {
                $this->export->getConfig()->setLastExportedId($item['fields']['entity_id']);
            }
            
            $item = $this->converter->apply($item);
            $this->container->append($item);
        }
        
        if (!count($data)) {
            $this->finalize();
        }
    
        $this->updateProgress();
    }
    
    /**
     * Update after process is finished
     */
    public function finalize()
    {
        $this->container->addFileEnding();
        
        $this->export->setStatus(Profile::STATUS_COMPLETE);
        try {
            switch ($this->export->getProfile()->getConfig()->getType()) {
                case Profile::SAVE_TYPE_FTP:
                    $this->ftpSent();
                    break;
                case Profile::SAVE_TYPE_EMAIL:
                    $this->emailSend();
                    break;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
    
    /**
     * Update profile after each iteration
     */
    public function updateProgress()
    {
        if ($this->export->getStatus() != Profile::STATUS_COMPLETE) {
            $this->export->setStatus(Profile::STATUS_PROCESSING);
        }
        
        $this->export->save();
    }
    
    /**
     * Sent export file to ftp
     */
    public function ftpSent()
    {
        $config = $this->export->getProfile()->getConfig();

        try {
            $result = $this->ftpConnect->open($this->getConnectionParams());
        
            if (!$result) {
                return false;
            }
            
            $path = trim($config->getPathFtp());
            if ($path) {
                $result = $this->ftpConnect->cd(rtrim($path, "/") . '/');
                
                if (!$result) {
                    return false;
                }
            }
            
            $result = $this->ftpConnect->write(
                basename($this->export->getFilename()),
                $this->export->getFilename()
            );
            
            $this->ftpConnect->close();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Ftp connection params
     *
     * @return array
     */
    protected function getConnectionParams()
    {
        $config = $this->export->getProfile()->getConfig();
        
        $params = [
            'host'     => trim($config->getFtp()),
            'user'     => trim($config->getUserFtp()),
            'password' => trim($config->getPassFtp()),
            'passive'  => trim($config->getPassmodeFtp()),
            'timeout'  => 10,
        ];
        
        if (strpos($params['host'], ':') !== false) {
            list($params['host'], $params['port']) = explode(':', $params['host']);
        }

        return $params;
    }

    /**
     * Send email with file
     */
    public function emailSend()
    {
        $config = $this->export->getProfile()->getConfig();

        $templateId = $this->scope->getValue('oei/email/template');
        if ($config->getTemplateEmail() != 'oei/email/template') {
            $templateId = $config->getTemplateEmail();
        }
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $this->store->getStore()->getId()])
                ->setTemplateVars([])
                ->setFrom($config->getSenderEmail())
                ->addTo($config->getSendEmail(), 'Export')
                ->createAttachment($this->export->getFilename(), basename($this->export->getFilename()))
                ->getTransport();
            
            $transport->sendMessage();
        } catch (\Exception $e) {
            return false;
        }
    }
}
