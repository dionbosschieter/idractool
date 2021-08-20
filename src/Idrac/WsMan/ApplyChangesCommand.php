<?php

namespace Idrac\WsMan;

use Exception;

/**
 * This command constructs a request that changes the PXE vlan
 */
class ApplyChangesCommand extends Request implements Command
{
    /** @var string */
    protected $actionUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_NICService/CreateTargetedConfigJob';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_NICService';

    /** @var array */
    protected $body = ['Target' => 'NIC.Integrated.1-1-1', 'AttributeName' => 'VLanId'];

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => 'DCIM_NICService',
        'SystemName' => 'DCIM:ComputerSystem',
        'Name' => 'DCIM:NICService'
    ];

    /**
     * @param string $vlan
     * @throws Exception when dns name is empty or not a number
     */
    public function __construct()
    {

        parent::__construct();

        $setAttribute = $this->dom->createElementNS($this->resourceUri, "p:CreateTargetedConfigJob_INPUT");
        $setAttribute->appendChild($this->dom->createElement("p:Target", "NIC.Integrated.1-1-1"));
        $setAttribute->appendChild($this->dom->createElement("p:ScheduledStartTime", "TIME_NOW"));
        $setAttribute->appendChild($this->dom->createElement("p:UntilTime", '20221111111111'));
        $this->body->appendChild($setAttribute);
    }
}
