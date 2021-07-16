<?php

namespace Idrac\WsMan;

/**
 * When send this Query Queries the idrac for a inventory of its BIOS settings
 */
class BIOSInventoryQuery extends Request implements DataQuery
{

    /** @var string */
    protected $actionUri = 'http://schemas.xmlsoap.org/ws/2004/09/enumeration/Enumerate';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_BIOSEnumeration';

    public function __construct()
    {
        parent::__construct();

        $setAttribute = $this->dom->createElement("n:Enumerate");
        $setAttribute->appendChild($this->dom->createElement("w:OptimizeEnumeration", ''));
        $setAttribute->appendChild($this->dom->createElement("w:MaxElements", 32000));
        $this->body->appendChild($setAttribute);
    }
}
