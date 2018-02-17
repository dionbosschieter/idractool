<?php

namespace Idrac\WsMan;

use Exception;

/**
 * This command constructs a request that changes the dns name of the idrac
 */
class SetIdracDnsCommand extends Request implements Command
{
    /** @var string */
    protected $actionUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_iDRACCardService/SetAttribute';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_iDRACCardService';

    /** @var array */
    protected $body = ['Target' => 'iDRAC.Embedded.1', 'AttributeName' => 'NIC.1#DNSRacName'];

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => 'DCIM_iDRACCardService',
        'SystemName' => 'DCIM:ComputerSystem',
        'Name' => 'DCIM:iDRACCardService'
    ];

    /**
     * @param string $dnsName
     * @throws Exception when dns name is empty
     */
    public function __construct($dnsName)
    {
        $dnsName = trim($dnsName);

        if (empty($dnsName)) {
            throw new Exception("Dns name cannot be empty");
        }

        if (strpos($dnsName, '.') !== false) {
            throw new Exception("Dns name '{$dnsName}' cannot contain dots");
        }

        parent::__construct();

        $setAttribute = $this->dom->createElementNS($this->resourceUri, "p:SetAttribute_INPUT");
        $setAttribute->appendChild($this->dom->createElement("p:Target", "iDRAC.Embedded.1"));
        $setAttribute->appendChild($this->dom->createElement("p:AttributeName", "NIC.1#DNSRacName"));
        $setAttribute->appendChild($this->dom->createElement("p:AttributeValue", $dnsName));
        $this->body->appendChild($setAttribute);
    }
}
