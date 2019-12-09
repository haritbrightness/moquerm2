<?php
namespace Swissup\Geoip\Helper;

use GeoIp2\Database\Reader;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Swissup\Geoip\Model\RecordFactory;

class Data
{
    const FILENAME_CONFIG = 'geoip/main/filename';
    const ENABLE_CONFIG   = 'geoip/main/enable';

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var RecordFactory
     */
    protected $recordFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param RemoteAddress $remoteAddress
     * @param ScopeConfigInterface $scopeConfig
     * @param RecordFactory $recordFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        ScopeConfigInterface $scopeConfig,
        RecordFactory $recordFactory,
        LoggerInterface $logger
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->scopeConfig = $scopeConfig;
        $this->recordFactory = $recordFactory;
        $this->logger = $logger;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::ENABLE_CONFIG,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param  string $ip
     * @return mixed
     */
    public function detect($ip = null)
    {
        $record = $this->recordFactory->create();

        if (!$ip) {
            $ip = $this->remoteAddress->getRemoteAddress();
        }

        $filepath = $this->getFilepath();
        if (!$filepath) {
            return $record;
        }

        try {
            $reader = new Reader($filepath);
            $rawRecord = $reader->city($ip);
            // $rawRecord = $reader->city('128.101.101.101');
            // $rawRecord = $reader->city('54.195.241.132');
            $record->update($rawRecord);
        } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
            // who cares
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $record;
    }

    /**
     * @return mixed
     */
    private function getFilepath()
    {
        $filename = $this->scopeConfig->getValue(
            self::FILENAME_CONFIG,
            ScopeInterface::SCOPE_STORE
        );
        $filename = basename($filename);

        $path = BP . '/var/swissup/geoip/' . $filename;
        if (!file_exists($path)) {
            $path = BP . '/vendor/swissup/module-geoip/' . $filename;
            if (!file_exists($path)) {
                $path = false;
            }
        }

        return $path;
    }
}
