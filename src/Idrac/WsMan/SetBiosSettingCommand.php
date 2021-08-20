<?php

namespace Idrac\WsMan;

use Exception;

/**
 * This command constructs a request that changes the PXE vlan
 */
class SetBiosSettingCommand extends Request implements Command
{
    const BIOSTYPENIC        = 'nic';

    public function __construct($attributeName, $attributeValue, $target = null, $biosType = null)
    {
        switch($biosType) {
            case self::BIOSTYPENIC:
            default:
                $namespace = 'DCIM_NICService';
                $name = 'DCIM:NICService';
                break;
        }
        $this->selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => $namespace,
        'SystemName' => 'DCIM:ComputerSystem',
        'Name' => $name
        ];

        $this->resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/'.$namespace;
        $this->actionUri = $this->resourceUri."/SetAttribute";
        $this->body = ['Target' => 'NIC.Integrated.1-1-1', 'AttributeName' => 'VLanMode'];

        parent::__construct();

        $setAttribute = $this->dom->createElementNS($this->resourceUri, "p:SetAttribute_INPUT");
        if ($target) {
            $setAttribute->appendChild($this->dom->createElement("p:Target", $target));
        }
        $setAttribute->appendChild($this->dom->createElement("p:AttributeName", $attributeName));
        $setAttribute->appendChild($this->dom->createElement("p:AttributeValue", $attributeValue));
        $this->body->appendChild($setAttribute);
    }
}
