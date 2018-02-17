<?php

namespace Idrac\WsMan;

/**
 * Interface for a WsMan query, these are the functions the wsman client expects
 */
interface DataQuery
{
    /**
     * Returns the WsMan XML for this command
     *
     * @return string
     */
    public function getAsXML();

    /**
     * Sets the a:To field in the xml
     *
     * @param string $endpoint
     */
    public function setEndpoint($endpoint);
}
