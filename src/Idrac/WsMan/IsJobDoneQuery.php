<?php

namespace Idrac\WsMan;

use Exception;

/**
 * With this query we can check if a job is done
 */
class IsJobDoneQuery extends Request implements DataQuery
{

    /** @var string */
    protected $actionUri = 'http://schemas.xmlsoap.org/ws/2004/09/transfer/Get';

    /** @var string */
    protected $resourceUri = 'http://schemas.dmtf.org/wbem/wscim/1/cim-schema/2/root/dcim/DCIM_LifecycleJob';

    public function __construct($jobId)
    {
        if (! preg_match('/JID\_\d+/', $jobId)) {
            throw new Exception("Incorrect Job ID (JID_\\d+) $jobId");
        }

        $this->selectorSet['InstanceID'] = $jobId;

        parent::__construct();
    }
}
