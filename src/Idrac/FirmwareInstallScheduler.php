<?php

namespace Idrac;

use Idrac\WsMan\Client;
use Idrac\WsMan\InstallFromUriCommand;
use Idrac\WsMan\JobResponse;
use Idrac\WsMan\ScheduleJobNowCommand;
use Idrac\WsMan\SoftwareIdentity;
use Idrac\WsMan\SoftwareInventoryResponse;
use Idrac\Log;

/**
 * This class is used for scheduling given firmware updates for given firmware identities
 * from a nfs share specified in the settings
 */
class FirmwareInstallScheduler
{

    /** @var Client */
    private $wsmanClient;

    /**
     * @param Client $wsmanClient wsmanClient of the Idrac for which we want to schedule firmwares updates
     */
    public function __construct(Client $wsmanClient)
    {
        $this->wsmanClient = $wsmanClient;
    }

    /**
     * Creates and schedules the install firmware jobs for the given firmwares and identities
     *
     * @param Firmware[] $firmwaresToSchedule
     * @param SoftwareIdentity[] $installedIdentities
     * @return JobResponse[] list of jobs
     */
    public function scheduleFirmwaresForIdentities($firmwaresToSchedule, $installedIdentities)
    {
        $jobs = [];

        foreach ($firmwaresToSchedule as $componentId => $dellFirmware) {
            $matchedSoftwareIdentity = $this->matchSoftwareIdentities($installedIdentities, $componentId);

            if (empty($matchedSoftwareIdentity)) {
                continue;
            }

            $downloadUrl = $dellFirmware->getDownloadUrl();

            $response = $this->wsmanClient->perform(new InstallFromUriCommand($downloadUrl, $matchedSoftwareIdentity));
            Log::info(get_class(), "Scheduling update installation for {$matchedSoftwareIdentity->getComponentName()}");
            $this->wsmanClient->perform(new ScheduleJobNowCommand($response->getJobId()));

            $jobs[] = $response;
        }

        return $jobs;
    }

    /**
     * Gets a software identity from a list by the componentId
     *
     * @param SoftwareIdentity[] $identities
     * @param int $componentId
     * @return null|SoftwareIdentity
     */
    private function matchSoftwareIdentities($identities, $componentId)
    {
        foreach ($identities as $softwareIdentity) {
            if ($softwareIdentity->getComponentId() == $componentId) {
                return $softwareIdentity;
            }
        }

        return null;
    }
}
