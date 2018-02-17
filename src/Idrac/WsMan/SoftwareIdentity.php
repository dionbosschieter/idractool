<?php

namespace Idrac\WsMan;

use DOMElement;
use Exception;

/**
 * This is a software identity (available/installed firmware), gets returned by a SoftwareInventory response
 */
class SoftwareIdentity
{
    const STATUS_INSTALLED = 'Installed';

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
    private function getValueOfTagName($tagName)
    {
        $nodeList = $this->data->getElementsByTagName($tagName);

        if ($nodeList->length == 0) {
            throw new Exception("TagName does not exists");
        }

        return $nodeList->item(0)->nodeValue;
    }

    /**
     * @return string
     */
    public function getInstanceId()
    {
        return $this->getValueOfTagName('InstanceID');
    }

    /**
     * @return string
     */
    public function getComponentName()
    {
        return $this->getValueOfTagName('ElementName');
    }

    /**
     * @return string
     */
    public function getComponentId()
    {
        return $this->getValueOfTagName('ComponentID');
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getValueOfTagName('Status');
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->getValueOfTagName('VersionString');
    }

    /**
     * @return string
     */
    public function isUpdateAble()
    {
        return $this->getValueOfTagName('Updateable');
    }

    /**
     * @return string
     */
    public function getDeviceId()
    {
        return $this->getValueOfTagName('DeviceID');
    }

    /**
     * @return string
     */
    public function getSubDeviceId()
    {
        return $this->getValueOfTagName('SubDeviceID');
    }

    /**
     * @return string
     */
    public function getVendorId()
    {
        return $this->getValueOfTagName('VendorID');
    }

    /**
     * @return string
     */
    public function getSubVendorId()
    {
        return $this->getValueOfTagName('SubVendorID');
    }
}
