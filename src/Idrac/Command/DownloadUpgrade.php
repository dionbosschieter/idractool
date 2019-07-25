<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;
use Idrac\PasswordManager;

class DownloadUpgrade extends Command
{
    protected function configure()
    {
        $this
            ->setName('download-upgrade')
            ->addArgument('host', InputArgument::REQUIRED, 'Host to connect to')
            ->setDescription('List all possible firmware upgrades and choose one to download');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hostInterpreter = new \Idrac\HostInterpreter($input->getArgument('host'));
        $servers = $hostInterpreter->getAllHosts();
        $user = 'root';
        $firmwareHandler = new FirmwareHandler();

        foreach ($servers as $hostname) {
            $output->writeln([
                "<info>Fetching firmwares to download for {$hostname}",
                '============</info>',
            ]);

            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, PasswordManager::getForHost($hostname));

            $response = $client->query(new WsMan\SystemViewQuery());
            $systemId = (int) $response->getValueOfTagName("SystemID");

            /** @var WsMan\SoftwareInventoryResponse $ids */
            $ids = $client->query(new WsMan\SoftwareInventoryQuery());
            $identities = $ids->getInstalledIdentities();

            $firmwaresToDownload = $firmwareHandler->getFirmwares($systemId, $identities);

            foreach ($firmwaresToDownload as $firmware) {
                $output->writeln("Downloading firmware: {$firmware->getFileName()} to " . __DIR__);

                $firmwareBlob = file_get_contents($firmware->getDownloadUrl());
                file_put_contents(__DIR__ . '/' . $firmware->getFileName(), $firmwareBlob);
            }

            $output->writeln('');
        }
    }
}
