<?php

namespace Idrac\WsMan;

use Exception;

/**
 * This command schedules a Job to run now
 * only needed on Jobs that need manual scheduling like the software updates
 */
class ScheduleJobNowCommand extends Request implements Command
{

    /** @var string */
    protected $actionUri = 'http://schemas.dell.com/wbem/wscim/1/cim-schema/2/DCIM_JobService/SetupJobQueue';

    /** @var string */
    protected $resourceUri = 'http://schemas.dell.com/wbem/wscim/1/cim-schema/2/DCIM_JobService';

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => 'DCIM_JobService',
        'SystemName' => 'Idrac',
        'Name' => 'JobService',
        '__cimnamespace' => 'root/dcim'
    ];

    /**
     * @param string $jobId
     * @throws Exception when an invalid job id is given
     */
    public function __construct($jobId)
    {
        if (! preg_match('/JID\_\d+/', $jobId)) {
            throw new Exception("Incorrect Job ID (JID_\\d+) $jobId");
        }

        parent::__construct();

        $setupJobQueue = $this->dom->createElementNS($this->resourceUri, "p:SetupJobQueue_INPUT");
        $setupJobQueue->appendChild($this->dom->createElement("p:JobArray", $jobId));
        $setupJobQueue->appendChild($this->dom->createElement("p:StartTimeInterval", "TIME_NOW"));
        $this->body->appendChild($setupJobQueue);
    }
}
