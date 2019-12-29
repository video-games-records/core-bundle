<?php
namespace VideoGamesRecords\CoreBundle\Command;

use ProjetNormandie\CommonBundle\Command\DefaultCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TeamCommand extends DefaultCommand
{
    protected function configure()
    {
        $this
            ->setName('vgr-core:team')
            ->setDescription('Command to update teams ranking')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idTeam',
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $idTeam = $input->getOption('idTeam');
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Team')->maj($idTeam);
                break;
            case 'maj-rank-point-chart':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Team')->majRankPointChart();
                break;
            case 'maj-rank-medal':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Team')->majRankMedal();
                break;
            case 'maj-rank-point-game':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Team')->majRankPointGame();
                break;
            case 'maj-rank-cup':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Team')->majRankCup();
                break;
            case 'maj-game-rank':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Team')->majGameRank();
                break;
        }
        $this->end($output);
        return true;
    }
}
