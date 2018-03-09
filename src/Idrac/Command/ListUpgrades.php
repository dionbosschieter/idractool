<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;
use Idrac\PasswordManager;

class ListUpgrades extends Command
{
    protected function configure()
    {
        $this
            ->setName('list-upgrades')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Host to connect to, also supports a comma separated')
            ->setDescription('List all possible firmware upgrades');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $servers = $input->getArgument('hosts');
        $servers = explode(',', $servers);
        $user = 'root';
        $firmwareHandler = new FirmwareHandler();

        foreach ($servers as $hostname) {
            $output->writeln([
                "<info>Possible Firmware Upgrades {$hostname}",
                '============</info>',
            ]);
            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, PasswordManager::getForHost($hostname));

            $response = $client->query(new WsMan\SystemViewQuery());
            $systemId = (int) $response->getValueOfTagName("SystemID");

            $ids = $client->query(new WsMan\SoftwareInventoryQuery());
            $identities = $ids->getInstalledIdentities();

            $firmwareHandler->getFirmwares($systemId, $identities);
            $output->writeln('');
        }
    }
}
