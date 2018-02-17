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
    private function getValueOfTagName($tagName)
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
    public function getMACAddress()
    {
        return $this->getValueOfTagName('CurrentMACAddress');
    }
}
