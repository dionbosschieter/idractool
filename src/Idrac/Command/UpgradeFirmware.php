<?php

namespace Idrac\Command;

use Idrac\FirmwareInstallScheduler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;
use Idrac\PasswordManager;

class UpgradeFirmware extends Command
{
    protected function configure()
    {
        $this
            ->setName('upgrade-firmware')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Host to connect to, also supports a comma separated')
            ->addOption('component')
            ->setDescription('Upgrades firmware for a specified component');
    }


        /**
         *         // schedule update
        $firmwareScheduler = new Idrac\FirmwareInstallScheduler($client);
        $jobs = $firmwareScheduler->scheduleFirmwaresForIdentities($firmwaresToSchedule, $identities);
        $jobTable[end($jobs)] = $client;

         */

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $servers = $input->getArgument('hosts');
        $component = $input->getOption('component');
        $servers = explode(',', $servers);
        $user = 'root';
        $firmwareHandler = new FirmwareHandler();

        foreach ($servers as $hostname) {
            $output->writeln([
                'Possible Firmware Upgrades',
                '============',
                '',
            ]);
            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, PasswordManager::getForHost($hostname));
            $firmwareScheduler = new FirmwareInstallScheduler($client);

            $response = $client->query(new WsMan\SystemViewQuery());
            $systemId = (int) $response->getValueOfTagName("SystemID");

            $ids = $client->query(new WsMan\SoftwareInventoryQuery());
            $identities = $ids->getInstalledIdentities();

            $firmwares = $firmwareHandler->getFirmwares($systemId, $identities);
            if (isset($firmwares[$component])) {
                $output->writeln("Found component {$component}, scheduling upgrade...");
                $jobs = $firmwareScheduler->scheduleFirmwaresForIdentities([$firmwares[$component]], $identities);
            }
        }
    }
}