<?php

namespace Idrac\WsMan;

use DOMDocument;
use Exception;
use Idrac\Firmware;
use Idrac\UriManager;
use Util;

/**
 * This command creates a install job to update a given firmware
 * from a given installation file on a nfs server
 */
class InstallFromUriCommand extends Request implements Command
{

    /** @var string */
    protected $actionUri = 'http://schemas.dell.com/wbem/wscim/1/cim-schema/2/DCIM_SoftwareInstallationService/InstallFromURI';

    /** @var string */
    protected $resourceUri = 'http://schemas.dell.com/wbem/wscim/1/cim-schema/2/DCIM_SoftwareInstallationService';

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => 'DCIM_SoftwareInstallationService',
        'SystemName' => 'IDRAC:ID',
        'Name' => 'SoftwareUpdate',
        '__cimnamespace' => 'root/dcim'
    ];

    /**
     * @param string $uri
     * @param SoftwareIdentity $identity current firmware to override/update
     * @throws Exception either when updatefilename is wrong or the nfs endpoint is not a legit IP
     */
    public function __construct(Firmware $firmware, SoftwareIdentity $identity)
    {
        if ( ! preg_match('/(\.*).exe/i', $firmware->getFileName())) {
            throw new Exception("Unknown type of fw installation file {$firmware->getFileName()}");
        }

        parent::__construct();

        $uri = UriManager::getUriForFirmware($firmware);

        $this->body = $this->createBodyXML($uri, $identity->getInstanceId());
    }

    /**
     * Creates the inner body xml to which we can append to the body
     *
     * @return string
     */
    private function createBodyXML($installUri, $targetInstallable)
    {
        $innerBody = $this->dom->createElementNS($this->resourceUri, 'p:InstallFromURI_INPUT');

        $uri = $this->dom->createElementNS($this->resourceUri, 'p:URI', $installUri);
        $innerBody->appendChild($uri);

        $target = $this->dom->createElementNS($this->resourceUri, 'p:Target');
        $target->setAttributeNS($this->xmlnsUri, 'xmlns:a', $this->envelope->getAttribute('xmlns:a'));
        $target->setAttributeNS($this->xmlnsUri, 'xmlns:w', $this->envelope->getAttribute('xmlns:w'));
        $innerBody->appendChild($target);

        $address = $this->dom->createElement('a:Address', 'http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous');
        $target->appendChild($address);

        $referenceParameters = $this->dom->createElement('a:ReferenceParameters');
        $target->appendChild($referenceParameters);

        $resourceURI = $this->dom->createElement('w:ResourceURI', 'http://schemas.dell.com/wbem/wscim/1/cim-schema/2/DCIM_SoftwareIdentity');
        $referenceParameters->appendChild($resourceURI);

        $selectorSet = $this->dom->createElement('w:SelectorSet');
        $referenceParameters->appendChild($selectorSet);

        $selector = $this->dom->createElement('w:Selector', $targetInstallable);
        $selector->setAttribute('Name', 'InstanceID');
        $selectorSet->appendChild($selector);

        return $this->body->appendChild($innerBody);
    }
}
