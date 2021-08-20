<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;
use Idrac\PasswordManager;

class ListNics extends Command
{
    protected function configure()
    {
        $this
            ->setName('list-nics')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Host to connect to, also supports a comma separated')
            ->addOption('write', 'w', InputOption::VALUE_OPTIONAL, 'Write the nic info for the hostname(s) to the given file', false)
            ->setDescription('List version on idrac devices');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Nics',
            '============',
            '',
        ]);

        $hostInterpreter = new \Idrac\HostInterpreter($input->getArgument('hosts'));
        $servers = $hostInterpreter->getAllHosts();

        $user = 'root';

        $writeData = [];

        // append if file already exists
        if ($file = $input->getOption('write')) {
            $writeData = json_decode(file_get_contents($file), true) ?? [];
        }

        foreach ($servers as $hostname) {
            $output->writeln([
                $hostname,
                '============',
            ]);

            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, PasswordManager::getForHost($hostname));
            /** @var WsMan\NicInventoryResponse $inventoryResponse */
            $inventoryResponse = $client->query(new WsMan\NicInventoryQuery());

            $nics = $inventoryResponse->getMainNics();
            $writeData[$hostname] = [];
            foreach ($nics as $index => $nic) {
                $output->writeln("{$nic->getInstanceID()} = {$nic->getDeviceDescription()} ({$nic->getMacAddress()}) '{$nic->getVendorName()} {$nic->getProductName()}'");
                $writeData[$hostname][] = $nic->getMACAddress();
            }

            if ($file = $input->getOption('write')) {
                file_put_contents($file, json_encode($writeData));
            }
        }
    }
}
