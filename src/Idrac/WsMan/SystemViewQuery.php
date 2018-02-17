<?php

namespace Idrac\WsMan;

/**
 * Gets basic system info of the drac
 */
class SystemViewQuery extends Request implements DataQuery
{

    /** @var string */
    protected $actionUri = 'http://schemas.xmlsoap.org/ws/2004/09/enumeration/Enumerate';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_SystemView';

    public function __construct()
    {
        parent::__construct();

        $enumerate = $this->dom->createElement("n:Enumerate");
        $enumerate->appendChild($this->dom->createElement("w:OptimizeEnumeration", ''));
        $enumerate->appendChild($this->dom->createElement("w:MaxElements", 32000));
        $this->body->appendChild($enumerate);
    }
}
