<?php

namespace Idrac\WsMan;

use Exception;
use Idrac\Log;

/**
 * Helper class to wait till a Idrac job is changed to a final state and debug its output
 */
class WaitForJob
{

    /**
     * Waits for the job to be finished, echo's the results to the terminal
     *
     * @param JobResponse $jobResponse
     * @param Client $client
     * @param string $ignore ignore this state
     */
    public static function waitFor(JobResponse $jobResponse, Client $client, $ignore = '')
    {
        self::waitForJobId($jobResponse->getJobId(), $client, $ignore);
    }

    /**
     * Waits for the job to be finished, echo's the results to the terminal
     *
     * @param string $jobId
     * @param Client $client
     * @param string $ignore ignore this state
     * @throws Exception
     */
    public static function waitForJobId($jobId, Client $client, $ignore = '')
    {
        while(true) {
            $response = $client->query(new IsJobDoneQuery($jobId));
            Log::info(get_called_class(), $response->getValueOfTagName('JobStatus') . ' ' . $response->getValueOfTagName('PercentComplete'));

            $status = $response->getValueOfTagName('JobStatus');

            if ($status === 'Failed') {
                throw new Exception("Job failed! {$response->getValueOfTagName('Message')}");
            }

            if ($status !== $ignore && in_array($status, ['Completed', 'Downloaded', 'Scheduled'])) {
                Log::info(get_called_class(), $response->getValueOfTagName('Message'));
                break;
            }

            sleep(10);
        }
    }

    /**
     * @param JobResponse[] $responseList
     * @param Client $client
     * @param string $ignore ignore this state
     */
    public static function waitTillAllDone($responseList, Client $client, $ignore = '')
    {
        foreach ($responseList as $response) {
            self::waitFor($response, $client, $ignore);
        }
    }
}
