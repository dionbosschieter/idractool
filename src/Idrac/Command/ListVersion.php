<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;
use Idrac\PasswordManager;

class ListVersion extends Command
{
    protected function configure()
    {
        $this
            ->setName('list-version')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Host to connect to, also supports a comma separated')
            ->setDescription('List version on idrac devices');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Versions',
            '============',
            '',
        ]);

        $hostInterpreter = new \Idrac\HostInterpreter($input->getArgument('hosts'));
        $servers = $hostInterpreter->getAllHosts();

        $user = 'root';

        foreach ($servers as $hostname) {
            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, PasswordManager::getForHost($hostname));
            $ids = $client->query(new WsMan\SoftwareInventoryQuery());
            $identities = $ids->getInstalledIdentities();
            foreach ($identities as $id) {
                $output->writeln($id->getComponentName() . ': ' . $id->getVersion());
            }
        }
    }
}
