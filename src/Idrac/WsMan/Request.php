<?php

namespace Idrac\WsMan;
use DOMDocument;

/**
 * Base class of a idrac wsman request (command|query)
 * contains functionality to construct the xml for the request
 */
abstract class Request
{

    /** @var array */
    protected $selectorSet;

    /** @var string */
    protected $actionUri;

    /** @var string */
    protected $resourceUri;

    /** @var \DOMElement */
    protected $body;

    /** @var \DOMElement */
    private $header;

    /** @var \DOMElement */
    private $to;

    /** @var string */
    protected $xmlnsUri = 'http://www.w3.org/2000/xmlns/';

    /** @var \DOMElement */
    protected $envelope;


    public function __construct()
    {
        $this->dom = new DOMDocument('1.0');
        $this->dom->formatOutput = true;
        $this->envelope = $this->dom->createElementNS("http://www.w3.org/2003/05/soap-envelope", 's:Envelope');

        $this->envelope->setAttributeNS($this->xmlnsUri, 'xmlns:a', 'http://schemas.xmlsoap.org/ws/2004/08/addressing');
        $this->envelope->setAttributeNS($this->xmlnsUri, 'xmlns:n', 'http://schemas.xmlsoap.org/ws/2004/09/enumeration');
        $this->envelope->setAttributeNS($this->xmlnsUri, 'xmlns:w', 'http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd');
        $this->envelope->setAttributeNS($this->xmlnsUri, 'xmlns:p', 'http://schemas.microsoft.com/wbem/wsman/1/wsman.xsd');

        $this->dom->appendChild($this->envelope);

        $this->generateHeaderStructure();
        $this->body = $this->dom->createElement('s:Body');
        $this->envelope->appendChild($this->body);
    }

    private function generateHeaderStructure()
    {
        $this->header = $this->dom->createElement('s:Header');
        $this->envelope->appendChild($this->header);

        $this->to = $this->dom->createElement('a:To');
        $resourceUri = $this->dom->createElement('w:ResourceURI', $this->resourceUri);
        $resourceUri->setAttribute("s:mustUnderstand", 'true');
        $replyTo = $this->dom->createElement('a:ReplyTo');
        $address = $this->dom->createElement('a:Address', 'http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous');
        $address->setAttribute("s:mustUnderstand", 'true');
        $replyTo->appendChild($address);
        $action = $this->dom->createElement('a:Action', $this->actionUri);
        $action->setAttribute("s:mustUnderstand", 'true');
        $maxEnvSize = $this->dom->createElement('w:MaxEnvelopeSize', '512000');
        $maxEnvSize->setAttribute("s:mustUnderstand", 'true');
        $messageId = $this->dom->createElement('a:MessageID', $this->getStaticMessage());
        $operationTimeout = $this->dom->createElement('w:OperationTimeout', 'PT60.000S');
        $this->appendChildsToElement($this->header, [$this->to, $resourceUri, $replyTo, $action, $maxEnvSize, $messageId, $operationTimeout]);
        $this->attachSelectorSetToHeaderBefore($operationTimeout);
    }

    /**
     * Add a list of childs to an element
     *
     * @param $element
     * @param array $childs
     */
    private function appendChildsToElement($element, array $childs)
    {
        foreach ($childs as $child) {
            $element->appendChild($child);
        }
    }

    /**
     * Returns the WsMan XML for this command
     *
     * @return string
     */
    public function getAsXML()
    {
        return $this->dom->saveXML($this->envelope);
    }

    /**
     * Sets the a:To field in the xml
     *
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->to->nodeValue = $endpoint;
    }

    /**
     * Constructs and attaches the selector set to the header before another element
     * Because it needs to be on this location
     *
     * @return string
     */
    private function attachSelectorSetToHeaderBefore(\DOMElement $before)
    {
        if (empty($this->selectorSet)) {
            return;
        }

        $selectorSet = $this->dom->createElement('w:SelectorSet');

        foreach ($this->selectorSet as $name => $value) {
            $selector = $this->dom->createElement("w:Selector", $value);
            $selector->setAttribute("Name", $name);
            $selectorSet->appendChild($selector);
        }

        $this->header->insertBefore($selectorSet, $before);
    }

    /**
     * Returns a static uid message because the idrac expects it and doesn't use it
     *
     * @return string
     */
    private function getStaticMessage()
    {
        return 'uuid:869A303E-5309-413D-A8B4-21C693223954';
    }
}
