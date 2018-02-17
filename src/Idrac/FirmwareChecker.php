<?php

namespace Idrac;

use Idrac\WsMan\SoftwareIdentity;
use Idrac\Log;

/**
 * This class is used for retrieving a list of firmwares that can be updated and a download url
 * This class contains logic for checking if firmwares in the dell catalog file are newer than the ones currently installed
 */
class FirmwareChecker
{

    /** @var CatalogReader */
    private $reader;

    /**
     * Takes the CatalogReader as input which has functionality of reading through the catalog file
     * and retrieving a firmware list for our system
     *
     * @param CatalogReader $reader
     */
    public function __construct(CatalogReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Returns the firmwares to download and schedule for a given system by its given identities
     *
     * @param int $systemId
     * @param SoftwareIdentity[] $identities
     * @return Firmware[]
     */
    public function getFirmwaresToSchedule($systemId, $identities)
    {
        $firmwaresToSchedules = [];

        foreach ($identities as $softwareIdentity) {
            $dellFirmwares = $this->reader->getSoftwareComponentsForIdentityAndSystem($softwareIdentity, $systemId);

            foreach ($dellFirmwares as $dellFirmware) {
                $this->log("Checking if ({$dellFirmware->getVersion()} > {$softwareIdentity->getVersion()})");
                if ($dellFirmware->getVersion() > $softwareIdentity->getVersion()) {
                    $this->log("Firmware found for {$softwareIdentity->getComponentId()} {$softwareIdentity->getComponentName()} {$dellFirmware->getPath()}  {$dellFirmware->getPackageId()} {$dellFirmware->getPackageType()}");

                    $firmwaresToSchedules[$softwareIdentity->getComponentId()] = $dellFirmware;
                }
            }
        }

        return $firmwaresToSchedules;
    }

    /**
     * Logs the given message for this class
     *
     * @param string $msg
     */
    private function log($msg)
    {
        Log::info(get_called_class(), $msg);
    }
}
