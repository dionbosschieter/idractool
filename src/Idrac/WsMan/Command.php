<?php

namespace Idrac\WsMan;

/**
 * Interface for a WsMan command, these are the functions the wsman client expects
 */
interface Command
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
