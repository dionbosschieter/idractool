<?php

namespace Idrac\WsMan;

use Exception;

/**
 * This command will remove all scheduled and old jobs
 */
class ClearAllJobsCommand extends Request implements Command
{

    /** @var string */
    protected $actionUri = 'http://schemas.dell.com/wbem/wscim/1/cim-schema/2/DCIM_JobService/DeleteJobQueue';

    /** @var string */
    protected $resourceUri = 'http://schemas.dell.com/wbem/wscim/1/cim-schema/2/DCIM_JobService';

    /** @var array */
    protected $selectorSet = [
        'SystemCreationClassName' => 'DCIM_ComputerSystem',
        'CreationClassName' => 'DCIM_JobService',
        'SystemName' => 'Idrac',
        'Name' => 'JobService',
        '__cimnamespace' => 'root/dcim',
        'JobID' => 'JID_CLEARALL'
    ];
}
