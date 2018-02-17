<?php

namespace Idrac\WsMan;

/**
 * Class ProcessReservedIdracConfigCommand
 */
class ProcessReservedIdracConfigCommand extends Request implements Command
{

    /** @var string */
    protected $actionUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_iDRACCardService/CreateTargetedConfigJob';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_iDRACCardService';

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => 'DCIM_iDRACCardService',
        'SystemName' => 'DCIM:ComputerSystem',
        'Name' => 'DCIM:iDRACCardService'
    ];

    public function __construct()
    {
        parent::__construct();

        $targetedJob = $this->dom->createElementNS($this->resourceUri, "p:CreateTargetedConfigJob_INPUT");
        $targetedJob->appendChild($this->dom->createElement("p:Target", "iDRAC.Embedded.1"));
        $targetedJob->appendChild($this->dom->createElement("p:ScheduledStartTime", "TIME_NOW"));
        $this->body->appendChild($targetedJob);
    }
}
