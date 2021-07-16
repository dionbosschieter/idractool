<?php

namespace Idrac\WsMan;

/**
 * Custom response for the NicInventoryQuery
 */
class NicInventoryResponse extends Response
{
    /** @var array */
    private $nics = [];

    /**
     * Get the Nic objects for this response
     *
     * @return Nic[]
     */
    public function getNics(): array
    {
        if (empty($this->nics)) {
            $this->nics = $this->getNicsFromXml();
        }

        return $this->nics;
    }

    /**
     * Return the Nic objects filtered on mac address
     *
     * @return Nic[]
     */
    public function getMainNics(): array
    {
        return array_filter($this->getNics(), function (Nic $nic) {
            return $nic->getMACAddress() !== '';
        });
    }

    /**
     * Converts the nics in xml to an array of Nic objects
     *
     * @return Nic[]
     */
    private function getNicsFromXml(): array
    {
        $list = [];

        foreach ($this->dom->getElementsByTagName('DCIM_NICView') as $item) {
            $list[] = new Nic($item);
        }

        return $list;
    }
}
