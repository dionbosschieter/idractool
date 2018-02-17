<?php

namespace Idrac\WsMan;

use DOMDocument;
use DOMXPath;

/**
 * Base for the WsMan responses
 */
abstract class Response
{
    /** @var string */
    protected $rawXML;

    /** @var DOMDocument */
    protected $dom;

    /** @var DOMXPath */
    protected $xpath;

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        $this->rawXML = $response;
        $this->dom = new DOMDocument();
        $this->dom->loadXML($this->rawXML);
        $this->xpath = new DOMXPath($this->dom);
    }

    public function getAsXML()
    {
        return $this->rawXML;
    }
}
