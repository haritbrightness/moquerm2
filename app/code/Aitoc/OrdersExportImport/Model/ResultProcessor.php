<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2019 Aitoc (https://www.aitoc.com)
 * @package Aitoc_OrdersExportImport
 */


namespace Aitoc\OrdersExportImport\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Aitoc\OrdersExportImport\Model\Processor;

/**
 * Class ExportProcessor
 */
class ResultProcessor extends AbstractProcessor
{
    /**
     * Line comment prefix
     */
    const ARCHIVE_EXT = '.zip';
    
    /**
     * @var Processor\Config\ResultConfigMapperFactory
     */
    protected $configMapperFactory;
    
    /**
     * Directory Handle
     */
    protected $directoryHandle;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Import
     */
    private $import;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        Processor\ConfigFactory $configFactory,
        Processor\ConverterFactory $converterFactory,
        Processor\ContainerFactory $containerFactory,
        Processor\EntityTypeFactory $entityTypeFactory,
        Processor\Config\ResultConfigMapperFactory $configMapperFactory
    ) {
        parent::__construct($configFactory, $converterFactory, $containerFactory, $entityTypeFactory);
        $this->configMapperFactory = $configMapperFactory;
        $this->directoryHandle = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }
    
    /**
     * Init and start processing
     *
     * @param Import $profile
     */
    public function execute($import)
    {
        if (!is_object($import) || !$import->getData()) {
            throw new \LocalizedException('Please set correct config to processor.');
        }
        
        $mapper = $this->configMapperFactory->create();
        $config = $mapper->toConfig($import);
        $this->import = $import;
        $this->config = $config;
    }
    
    /**
     * Check & prepare output file
     */
    protected function checkAndPrepare()
    {
        if (is_file($this->config->getFilename())) {
            $this->initContainer();
        } else {
            $file = $this->directoryHandle->getAbsolutePath($this->config->getFilename());
            $this->directoryHandle->touch($file);
            
            $this->initContainer();
            $this->container->addFileBeginning();
        }
    }
    
    /**
     * Add line
     *
     * @param string $content
     * @param mixed $message
     */
    public function append($content, $message)
    {
        $this->checkAndPrepare();
        if (is_array($message)) {
            $message = implode(' ', $message);
        }
        
        $this->container->writeCommented($content, $message);
    }
    
    /**
     * Update after process is finished
     *
     * @return null|string
     */
    public function finalize()
    {
        if (is_file($this->config->getFilename())) {
            if (!$this->container) { //@todo find why simetimes container are not initialized
                $this->checkAndPrepare();
            }
            $this->container->addFileEnding();
            return $this->pack() ?: $this->config->getFilename();
        }
    }
    
    /**
     * Replace file with archive
     *
     * @return null|string
     */
    protected function pack()
    {
        if (class_exists('\ZipArchive') && is_file($this->config->getFilename())) {
            $destinationZip = $this->directoryHandle->getAbsolutePath(
                $this->config->getFilename() . self::ARCHIVE_EXT
            );
            $zip = new \ZipArchive();
            $zip->open($destinationZip, \ZipArchive::CREATE);
            
            $zip->addFile(
                $this->directoryHandle->getAbsolutePath($this->config->getFilename()),
                basename($this->config->getFilename())
            );
            $zip->close();
            // delete original
            $this->directoryHandle->delete($this->config->getFilename());
            return $destinationZip;
        }
    }
}
