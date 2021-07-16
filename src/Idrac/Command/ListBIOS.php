<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;
use Idrac\PasswordManager;

class ListBIOS extends Command
{
    protected function configure()
    {
        $this
            ->setName('list-bios')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Host to connect to, also supports a comma separated')
            ->setDescription('List all settings queryable from the BIOS');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'BIOS',
            '============',
            '',
        ]);

        $hostInterpreter = new \Idrac\HostInterpreter($input->getArgument('hosts'));
        $servers = $hostInterpreter->getAllHosts();

        $user = 'root';

        foreach ($servers as $hostname) {
            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, PasswordManager::getForHost($hostname));
            /** @var WsMan\BIOSInventoryResponse $inventoryResponse */
            $inventoryResponse = $client->query(new WsMan\BIOSInventoryQuery());
            $output->writeln($inventoryResponse->getAsXML());
        }
    }
}
