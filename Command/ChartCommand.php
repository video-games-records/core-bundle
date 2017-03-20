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

    private $sglLoggerEnabled = false;
    private $stack = null;

    protected function configure()
    {
        $this
            ->setName('vgr:chart')
            ->setDescription('Command to update chart rankings for players and teams')
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
        $idChart = $input->getOption('idChart');
        switch ($function) {
            case 'maj-player':
                if ($idChart) {
                    $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:TeamChart')->maj($idChart);
                } else {
                    $this->majPlayer($output);
                }
                break;
            case 'maj-team':
                if ($idChart) {
                    $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:TeamChart')->maj($idChart);
                } else {
                    $this->majTeam($output);
                }
                break;
        }
        $this->end($output);
        return true;
    }

    /**
     * @param $input
     */
    private function init($input)
    {
        if ($input->getOption('debug')) {
            $this->sglLoggerEnabled = true;
            // Start setup logger
            $doctrine = $this->getContainer()->get('doctrine');
            $doctrineConnection = $doctrine->getConnection();
            $this->stack = new DebugStack();
            $doctrineConnection->getConfiguration()->setSQLLogger($this->stack);
            // End setup logger
        }
    }

    /**
     * @param $output
     */
    private function end($output)
    {
        if ($this->sglLoggerEnabled) {
            $output->writeln(sprintf('%s queries', count($this->stack->queries)));
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
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function majTeam(OutputInterface $output)
    {
        /** @var \VideoGamesRecords\CoreBundle\Repository\ChartRepository $chartRepository */
        $chartRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Chart');
        /** @var \VideoGamesRecords\CoreBundle\Repository\TeamChartRepository $teamChartRepository */
        $teamChartRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:TeamChart');
        /** @var \VideoGamesRecords\CoreBundle\Repository\TeamGroupRepository $teamGroupRepository */
        $teamGroupRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:TeamGroup');
        /** @var \VideoGamesRecords\CoreBundle\Repository\TeamGameRepository $teamGameRepository */
        $teamGameRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:TeamGame');
        /** @var \VideoGamesRecords\CoreBundle\Repository\TeamRepository $teamrRepository */
        $teamRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Team');

        if (false === $chartRepository->isMajTeamRunning()) {
            $chartRepository->goToMajTeam(self::NB_CHART_TO_MAJ);

            $teamList = array();
            $groupList = array();
            $gameList = array();

            $charts = $chartRepository->getChartToMajTeam();

            if (count($charts) > 0) {
                foreach ($charts as $chart) {
                    $teamList = array_unique(
                        array_merge($teamList, $teamChartRepository->maj($chart->getIdChart()))
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
                    $teamGroupRepository->maj($idGroup);
                }

                //----- Maj game
                foreach ($gameList as $idGame) {
                    $teamGameRepository->maj($idGame);
                    //@todo Maj MasterBadge
                }

                //----- Maj player
                foreach ($teamList as $idTeam) {
                    $teamRepository->maj($idTeam);
                }

                //----- Maj all teams
                $teamRepository->majRankPointChart();
                $teamRepository->majRankPointGame();
                $teamRepository->majRankMedal();
                $teamRepository->majRankCup();
            } else {
                $output->writeln("No record to maj");
            }
        } else {
            $output->writeln("vgr:chart maj-team is allready running");
        }
    }
}
