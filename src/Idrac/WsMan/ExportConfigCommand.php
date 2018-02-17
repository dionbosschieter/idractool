<?php

namespace Idrac\WsMan;

use Util;
use Exception;

/**
 * This command extracts a (idrac/lifecycle/bios) config out of the Idrac to a given nfs server
 */
class ExportConfigCommand extends Request implements Command
{
    /** @var string */
    protected $actionUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_LCService/ExportSystemConfiguration';

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

        $exportSystemConfiguration = $this->dom->createElementNS($this->resourceUri, "p:ExportSystemConfiguration_INPUT");
        $exportSystemConfiguration->appendChild($this->dom->createElement("p:IPAddress", $nfsEndpoint));
        $exportSystemConfiguration->appendChild($this->dom->createElement("p:ShareName", $nfsShareName));
        $exportSystemConfiguration->appendChild($this->dom->createElement("p:ShareType", 0));
        $exportSystemConfiguration->appendChild($this->dom->createElement("p:FileName", $fileName));
        $this->body->appendChild($exportSystemConfiguration);
    }
}
