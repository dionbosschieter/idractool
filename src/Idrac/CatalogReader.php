<?php

namespace Idrac;

use Exception;
use Idrac\WsMan\SoftwareIdentity;
use SimpleXMLElement;
use Idrac\Log;

/**
 * Reads the dell catalog file and helps determining if a SoftwareIdentity is the latest version
 */
class CatalogReader
{

    /** @var SimpleXMLElement */
    private $catalog;

    // Firmwares come in two types XP or 64
    const PACKAGE_XP = 'LWXP';
    const PACKAGE_64 = 'LW64'; // we need version 64

    /**
     * @param string $xmlUrl url of the catalog file
     */
    public function __construct($xmlUrl)
    {
        $this->catalog = simplexml_load_file($xmlUrl);
    }

    /**
     * Gets software identities that match the given systemid
     *
     * @param int $systemId system id of Idrac host
     * @throws Exception when systemid is not an integer
     * @return Firmware[]
     */
    public function getSoftwareComponentsForSystem($systemId)
    {
        $list = $this->filterSoftwareComponents($systemId);

        return Firmware::collection($list);
    }

    /**
     * Gets software identities that match this one
     *
     * @param SoftwareIdentity $identity
     * @param int $systemId system id of Idrac host
     * @throws Exception when systemid is not an integer
     * @return Firmware[]
     */
    public function getSoftwareComponentsForIdentityAndSystem(SoftwareIdentity $identity, $systemId)
    {
        $list = $this->filterSoftwareComponents($systemId);

        $list = array_filter($list, function (SimpleXMLElement $softComponent) use ($identity) {
            return $this->componentMatchesIdentity($softComponent, $identity);
        });

        return Firmware::collection($list);
    }

    /**
     * Strips alot of meuk software packages not needed for our systems
     *
     * @param int $systemId
     * @throws Exception when systemid is not an integer
     * @return array
     */
    private function filterSoftwareComponents($systemId)
    {
        if ( ! is_int($systemId)) {
            throw new Exception("systemId {$systemId} is not of type int");
        }

        $list = [];
        foreach ($this->catalog->SoftwareComponent as $softComponent) {
            if ($this->isFirmware($softComponent->ComponentType['value'])) {
                $list[] = $softComponent;
            }
        }

        $list = array_filter($list, function (SimpleXMLElement $softComponent) use ($systemId) {
            foreach ($softComponent->SupportedSystems as $brandContainer) {
                if ($this->brandContainerContainsSystemId($brandContainer, $systemId)) {
                    return true;
                }
            }

            return false;
        });

        // only take the 64 bit software packages
        $list = array_filter($list, function (SimpleXMLElement $softComponent) {
            return $this->getPackageTypeOfComponent($softComponent) === self::PACKAGE_64;
        });

        return $list;
    }

    /**
     * Check if this software component matches our SoftwareIdentity
     *
     * @param SimpleXMLElement $softComponent
     * @param SoftwareIdentity $identity
     * @return bool
     */
    private function componentMatchesIdentity(SimpleXMLElement $softComponent, SoftwareIdentity $identity)
    {
        // loop through the supported device list
        foreach ($softComponent->SupportedDevices->Device as $device) {
            if ($this->deviceMatchesIdentity($identity, $device)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the supported-device in the catalog matches the device of our SoftwareIdentity
     *
     * @param SoftwareIdentity $identity
     * @param SimpleXMLElement $device
     * @return bool
     */
    private function deviceMatchesIdentity(SoftwareIdentity $identity, SimpleXMLElement $device)
    {
        $identityComponentId = $identity->getComponentId();

        if (! empty($identityComponentId) && $device['componentID'] == $identityComponentId) {
            return true;
        }

        if (isset($device->PCIInfo)) {
            $pciInfo = $device->PCIInfo;

            if ($pciInfo['deviceID'] == $identity->getDeviceId() &&
                $pciInfo['subDeviceID'] == $identity->getSubDeviceId() &&
                $pciInfo['vendorID'] == $identity->getVendorId() &&
                $pciInfo['subVendorID'] == $identity->getSubVendorId()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the given ComponentType matches any of the firmware types
     *
     * @param string $value ComponentType
     * @return bool
     */
    private function isFirmware($value)
    {
        return in_array($value, ['FRMW', 'BIOS']);
    }

    /**
     * Check if the given catalog model's systemId equals to the given systemId
     *
     * @param SimpleXMLElement $model
     * @param int $systemId
     * @return bool
     */
    private function modelEqualsSystemId(SimpleXMLElement $model, $systemId)
    {
        $modelSystemId = hexdec($model['systemID']);

        return $modelSystemId === $systemId;
    }

    /**
     * Checks if the system id is present in the brand's models
     *
     * @param SimpleXMLElement $brandContainer
     * @param int $systemId
     * @return bool
     */
    private function brandContainerContainsSystemId(SimpleXMLElement $brandContainer, $systemId)
    {
        foreach ($brandContainer->Brand->Model as $model) {
            if ($this->modelEqualsSystemId($model, $systemId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the software package type of the catalog software component
     *
     * @param SimpleXMLElement $softComponent
     * @return string
     */
    private function getPackageTypeOfComponent($softComponent)
    {
        return (string) $softComponent['packageType'];
    }
}
