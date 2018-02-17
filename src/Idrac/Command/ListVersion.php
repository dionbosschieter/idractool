<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;

class ListVersion extends Command
{
    protected function configure()
    {
        $this
            ->setName('list-version')
            ->addArgument('host', InputArgument::REQUIRED, 'Host to connect to')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
            ->setDescription('List version on idrac devices');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Versions',
            '============',
            '',
        ]);

        $servers = [$input->getArgument('host')];
        $user = 'root';
        $password = $input->getArgument('password');
        $firmwareHandler = new FirmwareHandler();

        foreach ($servers as $hostname) {
            $success = false;
            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, $password);
            $ids = $client->query(new WsMan\SoftwareInventoryQuery());
            $identities = $ids->getInstalledIdentities();
            foreach ($identities as $id) {
                $output->writeln($id->getComponentName() . ': ' . $id->getVersion());
            }
        }
    }
}
