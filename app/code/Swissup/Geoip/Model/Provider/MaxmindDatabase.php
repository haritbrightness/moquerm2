<?php

namespace Swissup\Geoip\Model\Provider;

use GeoIp2\Database\Reader;
use Magento\Framework\Exception\NotFoundException;

class MaxmindDatabase extends MaxmindAbstract
{
    const FILENAME_CONFIG = 'geoip/main/filename';

    /**
     * @return Client
     */
    protected function getReader()
    {
        return new Reader($this->getFilepath());
    }

    /**
     * @return boolean
     */
    public function isCacheable()
    {
        return false;
    }

    /**
     * @return string
     * @throws NotFoundException
     */
    private function getFilepath()
    {
        $filename = $this->getConfigValue(self::FILENAME_CONFIG);
        $filename = basename($filename);

        $path = BP . '/var/swissup/geoip/' . $filename;
        if (!file_exists($path)) {
            $path = BP . '/vendor/swissup/module-geoip/' . $filename;
            if (!file_exists($path)) {
                throw new NotFoundException(__('Maxmind database file was not found.'));
            }
        }

        return $path;
    }
}
