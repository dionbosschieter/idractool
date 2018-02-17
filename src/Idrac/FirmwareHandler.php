<?php

namespace Idrac;

use ErrorCodes;
use Exception;
use Idrac\WsMan\SoftwareIdentity;

/**
 * This class is responsible for connecting other firmware classes to be able to schedule firmwares
 */
class FirmwareHandler
{

    /** @var string */
    private $catalogZippedXMLFile = "https://downloads.dell.com/catalog/Catalog.xml.gz";

    /** @var CatalogReader */
    private $cachedCatalogReader;

    /**
     * Returns the firmware updates that can be installed
     *
     * @param int $systemId
     * @param SoftwareIdentity[] $identities
     * @return Firmware[]
     */
    public function getFirmwares($systemId, $identities)
    {
        $reader = $this->getCatalogReader();
        $firmwareUpdater = new FirmwareChecker($reader);

        return $firmwareUpdater->getFirmwaresToSchedule($systemId, $identities);
    }

    /**
     * Get the catalog reader pointing to the live catalog file
     *
     * @return CatalogReader
     */
    private function getCatalogReader()
    {
        if (!$this->cachedCatalogReader) {
            $this->cachedCatalogReader = new CatalogReader("compress.zlib://{$this->catalogZippedXMLFile}");
        }

        return $this->cachedCatalogReader;
    }
}
