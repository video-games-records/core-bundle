<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\Logging\DebugStack;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ChartCommand extends ContainerAwareCommand
{
    const NB_CHART_TO_MAJ = 1;

    private $sglLoggerEnabled = true;
    private $stack = null;

    protected function configure()
    {
        $this
            ->setName('vgr:chart')
            ->setDescription('Greet someone')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idChart',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init();
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $idChart = $input->getOption('idChart');
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->maj($idChart);
                break;
            case 'maj-player':
                $this->majPlayer($output);
                break;
            case 'maj-team':
                $this->majTeam($output);
                break;
        }

        return true;
    }

    private function init()
    {
        if ($this->sglLoggerEnabled) {
            // Start setup logger
            $doctrine = $this->getContainer()->get('doctrine');
            $doctrineConnection = $doctrine->getConnection();
            $this->stack = new DebugStack();
            $doctrineConnection->getConfiguration()->setSQLLogger($this->stack);
            // End setup logger
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function majPlayer(OutputInterface $output)
    {
        /** @var \VideoGamesRecords\CoreBundle\Repository\ChartRepository $chartRepository */
        $chartRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Chart');
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository $playerChartRepository */
        $playerChartRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerChart');
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository $playerGroupRepository */
        $playerGroupRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup');
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository $playerGameRepository */
        $playerGameRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerGame');
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerRepository $playerRepository */
        $playerRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player');

        if (false === $chartRepository->isMajPlayerRunning()) {
            $chartRepository->goToMajPlayer(self::NB_CHART_TO_MAJ);

            $playerList = array();
            $groupList = array();
            $gameList = array();

            $charts = $chartRepository->getChartToMajPlayer();

            if (count($charts) > 0) {
                foreach ($charts as $chart) {
                    $playerList = array_unique(
                        array_merge($playerList, $playerChartRepository->maj($chart->getIdChart()))
                    );

                    //----- Group
                    if (!in_array($chart->getIdGroup(), $groupList)) {
                        $groupList[] = $chart->getIdGroup();
                    }
                    //----- Game
                    if (!in_array($chart->getGroup()->getIdGame(), $gameList)) {
                        $gameList[] = $chart->getGroup()
                            ->getIdGame();
                    }
                }

                //----- Maj group
                foreach ($groupList as $idGroup) {
                    $playerGroupRepository->maj($idGroup);
                }

                //----- Maj game
                foreach ($gameList as $idGame) {
                    $playerGameRepository->maj($idGame);
                    //@todo Maj MasterBadge
                }

                //----- Maj player
                foreach ($playerList as $idPlayer) {
                    $playerRepository->maj($idPlayer);
                    //@todo Maj badge chart
                    //@todo Maj badge proof
                }

                //----- Maj all players
                $playerRepository->majRankPointChart();
                $playerRepository->majRankPointGame();
                $playerRepository->majRankMedal();
                $playerRepository->majRankCup();
                $playerRepository->majRankProof();
                //@todo MAJ badge best player on country
            } else {
                $output->writeln("No record to maj");
            }
        } else {
            $output->writeln("vgr:chart maj-player is allready running");
        }

        if ($this->sglLoggerEnabled) {
            $output->writeln(sprintf('%s queries', $this->stack->queries));
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function majTeam(OutputInterface $output)
    {
        /** @var \VideoGamesRecords\CoreBundle\Repository\ChartRepository $chartRepository */
        $chartRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Chart');
        if (false === $chartRepository->isMajTeamRunning()) {
            //@todo
        } else {
            $output->writeln("vgr:chart maj-team is allready running");
        }

        if ($this->sglLoggerEnabled) {
            $output->writeln(sprintf('%s queries', $this->stack->queries));
        }
    }
}
