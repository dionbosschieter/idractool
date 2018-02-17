<?php

namespace Idrac\WsMan;

use Util;
use Exception;

/**
 * This command imports a (idrac/lifecycle/bios) config into the Idrac from a given nfs server
 */
class ImportConfigCommand extends Request implements Command
{
    /** @var string */
    protected $actionUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_LCService/ImportSystemConfiguration';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_LCService';

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => 'DCIM_LCService',
        'SystemName' => 'DCIM:ComputerSystem',
        'Name' => 'DCIM:LCService'
    ];

    /**
     * @param string $nfsEndpoint
     * @param string $nfsShareName
     * @param string $fileName
     * @throws Exception when ip of nfs server is invalid
     */
    public function __construct($nfsEndpoint, $nfsShareName, $fileName)
    {
        if (! Util::isValidIP($nfsEndpoint)) {
            throw new Exception("A valid ip should be given for the nfs endpoint");
        }

        parent::__construct();

        $importSystemConfiguration = $this->dom->createElementNS($this->resourceUri, "p:ImportSystemConfiguration_INPUT");
        $importSystemConfiguration->appendChild($this->dom->createElement("p:IPAddress", $nfsEndpoint));
        $importSystemConfiguration->appendChild($this->dom->createElement("p:ShareName", $nfsShareName));
        $importSystemConfiguration->appendChild($this->dom->createElement("p:ShareType", 0));
        $importSystemConfiguration->appendChild($this->dom->createElement("p:FileName", $fileName));
        $importSystemConfiguration->appendChild($this->dom->createElement("p:ShutdownType", 0));
        $importSystemConfiguration->appendChild($this->dom->createElement("p:TimeToWait", 300));
        $importSystemConfiguration->appendChild($this->dom->createElement("p:EndHostPowerState", 0));
        $this->body->appendChild($importSystemConfiguration);
    }
}
