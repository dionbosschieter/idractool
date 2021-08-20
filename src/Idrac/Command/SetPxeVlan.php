<?php

namespace Idrac\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Idrac\WsMan;
use Idrac\PasswordManager;

class SetPxeVlan extends Command
{
    protected function configure()
    {
        $this
            ->setName('set-vlan')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Host to connect to, also supports a comma separated')
            ->addArgument('vlan', InputArgument::REQUIRED, 'Vlan Id to set')
            ->setDescription('Set PXE Vlan for interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Set Vlan',
            '============',
            '',
        ]);

        $hostInterpreter = new \Idrac\HostInterpreter($input->getArgument('hosts'));
        $vlan = $input->getArgument('vlan');
        $servers = $hostInterpreter->getAllHosts();

        $user = 'root';

        foreach ($servers as $hostname) {
            $output->writeln([
                $hostname,
                '============',
            ]);

            $url = WsMan\Client::getUrl($hostname);
            $client = new WsMan\Client($url, $user, PasswordManager::getForHost($hostname));

            $response = $client->perform(new WsMan\SetBiosSettingCommand ('VlanMode', 'Enabled', 'NIC.Integrated.1-1-1', WsMan\SetBiosSettingCommand::BIOSTYPENIC));
            $output->writeln('Enable Vlan: ' . $response->getMessage());
            if ($response->getReturnValue() !== 0 && $response->isAlreadyCreated() === false && $response->isAttributeDoesNotExist() === false) {
                throw new \Exception("Unexpected error occcured during vlan setting");
            }

            $response = $client->perform(new WsMan\SetBiosSettingCommand ('VLanId', $vlan,'NIC.Integrated.1-1-1', WsMan\SetBiosSettingCommand::BIOSTYPENIC));
            $output->writeln('Set Vlan to '. $vlan . ': ' . $response->getMessage());
            if ($response->getReturnValue() !== 0 && $response->isAlreadyCreated() === false) {
                throw new \Exception("Unexpected error occcured during vlan setting");
            }

            $response = $client->perform(new WsMan\ApplyChangesCommand());
            $output->writeln('Config apply');
            if ($response->getReturnValue() !== 0 && $response->getReturnValue() !== 4096 && $response->isAlreadyCreated() === false) {
                $output->writeln($response->getAsXML());
                throw new \Exception("Unexpected error occcured during config apply");
            }
        }
    }
}
