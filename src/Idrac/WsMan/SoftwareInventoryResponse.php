<?php

namespace Idrac\WsMan;

/**
 * Custom response for the SoftwareInventoryQuery
 */
class SoftwareInventoryResponse extends Response
{
    /** @var array */
    private $identities = [];

    /**
     * Get the SoftwareIdentity objects for this response
     *
     * @return SoftwareIdentity[]
     */
    public function getSoftwareIdentities()
    {
        if (empty($this->identities)) {
            $this->identities = $this->getIdentities();
        }

        return $this->identities;
    }

    /**
     * Return return the SoftwareIdentity objects filtered on SoftwareIdentity::STATUS_INSTALLED
     *
     * @return SoftwareIdentity[]
     */
    public function getInstalledIdentities()
    {
        return array_filter($this->getSoftwareIdentities(), function (SoftwareIdentity $identiy) {
            return $identiy->getStatus() == SoftwareIdentity::STATUS_INSTALLED;
        });
    }

    /**
     * Converts the software identities in xml to an array of SoftwareIdentity objects
     *
     * @return SoftwareIdentity[]
     */
    private function getIdentities()
    {
        $list = [];

        foreach ($this->dom->getElementsByTagName('DCIM_SoftwareIdentity') as $item) {
            $list[] = new SoftwareIdentity($item);
        }

        return $list;
    }
}
