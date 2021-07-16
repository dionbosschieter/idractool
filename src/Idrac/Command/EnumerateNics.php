<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;
use Idrac\PasswordManager;

class EnumerateNics extends Command
{
    protected function configure()
    {
        $this
            ->setName('enumerate-nics')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Host to connect to, also supports a comma separated')
            ->setDescription('Get the BIOS configuration for the NICs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'NIC Config XML',
            '============',
            '',
        ]);

        $hostInterpreter = new \Idrac\HostInterpreter($input->getArgument('hosts'));
        $servers = $hostInterpreter->getAllHosts();

        $user = 'root';

        foreach ($servers as $hostname) {
            $output->writeln("# {$hostname}");

            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, PasswordManager::getForHost($hostname));
             /** @var WsMan\NicIntegerEnumerationResponse $response */
            $response = $client->query(new WsMan\NicIntegerEnumerationQuery);
            foreach ($response->getNicConfigs() as $config) {
                $output->writeln("{$config->getNicInstanceId()} Vlan id = {$config->getVlanId()}");
            }

            /** @var WsMan\NicEnumerationResponse $response */
            $response = $client->query(new WsMan\NicEnumerationQuery);
            foreach ($response->getNicConfigs() as $config) {
                $output->writeln("{$config->getNicInstanceId()} Vlan mode = {$config->getVlanMode()}");
            }
        }
    }
}
