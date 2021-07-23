<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\FirmwareHandler;
use Idrac\PasswordManager;

class EnableImpi extends Command
{
    protected function configure()
    {
        $this
            ->setName('enable-ipmi')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Host to connect to, also supports a comma separated')
            ->setDescription('Enable or Disable IPMI over LAN');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Enable/Disable IPMI',
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
            /** @var WsMan\JobResponse $response */
            $response = $client->perform(new WsMan\EnableIpmiCommand);
            $output->writeln($response->getMessage());

            if ($response->getReturnValue() === 0) {
                $response = $client->perform(new WsMan\ApplyConfigCommand(WsMan\EnableIpmiCommand::SERVICE, WsMan\EnableIpmiCommand::TARGET));
                $output->writeln("Got Job id: {$response->getJobId()}");

            }
        }
    }
}
