<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BadgeCommand extends DefaultCommand
{
    protected function configure()
    {
        $this
            ->setName('vgr-core:badge')
            ->setDescription('Command to maj master badge for players and teams')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idGame',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                ''
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        $idGame = $input->getOption('idGame');
        switch ($function) {
            case 'maj':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majUserBadge();
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majPlayerBadge();
                break;
            case 'maj-player-master':
                $game = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Game')->find($idGame);
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majMasterBadge($game);
                break;
            case 'maj-team-master':
                $game = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Game')->find($idGame);
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:TeamBadge')->majMasterBadge($game);
                break;
        }
        $this->end($output);
        return true;
    }
}
