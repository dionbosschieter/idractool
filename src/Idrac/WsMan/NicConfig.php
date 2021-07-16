<?php

namespace Idrac\WsMan;

class NicConfig
{

    /** @var array */
    private $data;
    /**
     * @var string
     */
    private $nicInstanceId;

    public function __construct(string $nicInstanceId, array $config)
    {
        $this->nicInstanceId = $nicInstanceId;
        $this->data = $config;
    }

    private function getValueOfAttributeName($tagName): string
    {
        foreach ($this->data as $configData) {
            if ($configData['name'] === $tagName) {
                return $configData['value'];
            }
        }

        return '';
    }

    public function getVlanMode(): string
    {
        return $this->getValueOfAttributeName('VLanMode');
    }

    public function getVlanId(): string
    {
        return $this->getValueOfAttributeName('VLanId');
    }

    public function getNicInstanceId(): string
    {
        return $this->nicInstanceId;
    }
}