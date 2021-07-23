<?php

namespace Idrac\WsMan;

/**
 * This command constructs a request that enables IPMI
 */
class EnableIpmiCommand extends Request implements Command
{

    public const SERVICE = 'DCIM_iDRACCardService';

    public const TARGET = 'iDRAC.Embedded.1';

    /** @var string */
    protected $actionUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/' . self::SERVICE . '/SetAttribute';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/' . self::SERVICE;

    /** @var array */
    protected $body = ['Target' => self::TARGET, 'AttributeName' => 'IPMILan.1#Enable'];

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => self::SERVICE,
        'SystemName' => 'DCIM:ComputerSystem',
        'Name' => 'DCIM:iDRACCardService'
    ];

    public function __construct($enable = true)
    {
        parent::__construct();

        $value = 'Enabled';
        if (!$enable) {
            $value = 'Disabled';
        }

        $setAttribute = $this->dom->createElementNS($this->resourceUri, "p:SetAttribute_INPUT");
        $setAttribute->appendChild($this->dom->createElement("p:Target", self::TARGET));
        $setAttribute->appendChild($this->dom->createElement("p:AttributeName", "IPMILan.1#Enable"));
        $setAttribute->appendChild($this->dom->createElement("p:AttributeValue", $value));
        $this->body->appendChild($setAttribute);
    }
}
