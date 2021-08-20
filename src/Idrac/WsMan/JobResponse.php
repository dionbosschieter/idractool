<?php

namespace Idrac\WsMan;

use Exception;

/**
 * This is a response on a Command it gives us the abbility to extract a job id
 */
class JobResponse extends Response
{

    const STATUS_FAILED = "Failed";
    const STATUS_COMPLETED = "Completed";

    /**
     * Retrieves the job id from the XML
     *
     * @return string
     * @throws Exception when wsman namespace is non existing
     */
    public function getJobId()
    {
        $obj = $this->xpath->query('.//wsman:Selector[@Name="InstanceID"]');

        return $obj->item(0)->nodeValue;
    }

    public function getMessage(): string
    {
        $obj = $this->xpath->query('.//n1:Message');

        return $obj->item(0)->nodeValue ?? '';
    }

    public function getReturnValue(): int
    {
        $obj = $this->xpath->query('.//n1:ReturnValue');

        return intval($obj->item(0)->nodeValue);
    }

    public function isAlreadyCreated(): bool
    {
        return strpos($this->getMessage(), 'Configuration job already created') !== false;
    }

    public function isAttributeDoesNotExist(): bool
    {
        return strpos($this->getMessage(), 'Invalid AttributeName') !== false;
    }
}
