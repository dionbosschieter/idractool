<?php

namespace Idrac\WsMan;

/**
 * This command constructs a request that applies the previously pending set attributes
 */
class ApplyConfigCommand extends Request implements Command
{
    /** @var string */
    protected $actionUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/%s/CreateTargetedConfigJob';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/%s';

    /** @var array */
    protected $body = ['Target' => '',  'AttributeName' => 'IPMILan.1#Enable'];

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => '',
        'SystemName' => 'DCIM:ComputerSystem',
        'Name' => 'DCIM:%s'
    ];

    public function __construct(string $service, string $target, int $rebootType = 0)
    {
        $this->actionUri = sprintf($this->actionUri, $service);
        $this->resourceUri = sprintf($this->resourceUri, $service);

        $this->selectorSet['CreationClassName'] = $service;
        list(,$servicePart) = explode('_', $service);
        $this->selectorSet['Name'] = sprintf($this->selectorSet['Name'], $servicePart);

        $this->body['Target'] = $target;

        parent::__construct();

        $setAttribute = $this->dom->createElementNS($this->resourceUri, "p:CreateTargetedConfigJob_INPUT");
        $setAttribute->appendChild($this->dom->createElement("p:Target", $target));
        if ($rebootType !== 0) {
            $setAttribute->appendChild($this->dom->createElement("p:RebootJobType", $rebootType));
            $setAttribute->appendChild($this->dom->createElement("p:UntilTime", '20111111111111'));
        }
        $setAttribute->appendChild($this->dom->createElement("p:ScheduledStartTime", 'TIME_NOW'));
        $this->body->appendChild($setAttribute);
    }
}
