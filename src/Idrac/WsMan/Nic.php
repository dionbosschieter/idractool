<?php

namespace Idrac\WsMan;

use DOMElement;

/**
 * This is a network interface card, gets returned by a NicInventory response
 */
class Nic
{
    
    /** @var DOMElement */
    private $data;

    /**
     * @param DOMElement $element
     */
    public function __construct(DOMElement $element)
    {
        $this->data = $element;
    }

    /**
     * Returns the value of the given tag
     *
     * @param string $tagName
     * @return string
     * @throws Exception
     */
    private function getValueOfTagName($tagName): string
    {
        $nodeList = $this->data->getElementsByTagName($tagName);

        if ($nodeList->length == 0) {
            throw new Exception("TagName does not exists");
        }

        return $nodeList->item(0)->nodeValue;
    }

    /**
     * Returns the mac address of this nic
     *
     * @return string
     */
    public function getMACAddress(): string
    {
        return $this->getValueOfTagName('CurrentMACAddress');
    }

    /**
     * Returns the description of the nic
     *
     * @return string
     */
    public function getDeviceDescription(): string
    {
        return $this->getValueOfTagName('DeviceDescription');
    }

    /**
     * Returns the product name of this nic
     *
     * @return string
     */
    public function getProductName(): string
    {
        return $this->getValueOfTagName('ProductName');
    }

    /**
     * Returns the insntance id of this nic
     *
     * @return string
     */
    public function getInstanceID(): string
    {
        return $this->getValueOfTagName('InstanceID');
    }

    /**
     * Returns the vendor name of this nic
     *
     * @return string
     */
    public function getVendorName(): string
    {
        return $this->getValueOfTagName('VendorName');
    }
}
