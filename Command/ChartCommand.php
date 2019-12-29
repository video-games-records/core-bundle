<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\CommonBundle\Command\DefaultCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ChartCommand extends DefaultCommand
{
    const NB_CHART_TO_MAJ = 1000;

    protected function configure()
    {
        $this
            ->setName('vgr-core:chart')
            ->setDescription('Command to update chart rankings for players')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'What do you want to do?'
            )
            ->addOption(
                'idChart',
                null,
                InputOption::VALUE_REQUIRED,
                'Chart identifier'
            )
            ->addOption(
                'debug',
                'd',
                InputOption::VALUE_NONE,
                'Debug option (sql)'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return bool
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        $idChart  = $input->getOption('idChart');

        switch ($function) {
            case 'maj-player':
                if ($idChart) {
                    $chart = $this->doctrine->getRepository('VideoGamesRecordsCoreBundle:Chart')->find($idChart);
                    $this->updateCharts([$chart]);
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
     * @param OutputInterface $output
     * @throws \Exception
     */
    private function majPlayer(OutputInterface $output)
    {
        /** @var \VideoGamesRecords\CoreBundle\Repository\ChartRepository $chartRepository */
        $chartRepository = $this->doctrine->getRepository('VideoGamesRecordsCoreBundle:Chart');

        if ($chartRepository->isMajPlayerRunning()) {
            $output->writeln('vgr:chart maj-player is already running');

            return;
        }
        $chartRepository->goToMajPlayer(self::NB_CHART_TO_MAJ);


        $charts = $chartRepository->getChartToMajPlayer();
        $this->updateCharts($charts);

        $output->writeln(sprintf('%d chart(s) updated', count($charts)));
    }

    /**
     * @param array $charts
     * @throws \Exception
     */
    public function updateCharts(array $charts)
    {
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository $playerChartRepository */
        $playerChartRepository = $this->doctrine->getRepository('VideoGamesRecordsCoreBundle:PlayerChart');
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository $playerGroupRepository */
        $playerGroupRepository = $this->doctrine->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup');
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository $playerGameRepository */
        $playerGameRepository = $this->doctrine->getRepository('VideoGamesRecordsCoreBundle:PlayerGame');
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerRepository $playerRepository */
        $playerRepository = $this->doctrine->getRepository('VideoGamesRecordsCoreBundle:Player');
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository $playerBadgeRepository */
        $playerBadgeRepository = $this->doctrine->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge');
        $playerList = [];
        $groupList  = [];
        $gameList   = [];
        $countryList = [];

        foreach ($charts as $chart) {
            $idGroup = $chart->getGroup()->getId();
            $idGame  = $chart->getGroup()->getGame()->getId();
            //----- Player
            $playerList = array_merge($playerList, $playerChartRepository->maj($chart));
            //----- Group
            if (!isset($groupList[$idGroup])) {
                $groupList[$idGroup] = $chart->getGroup();
            }
            //----- Game
            if (!isset($gameList[$idGame])) {
                $gameList[$idGame] = $chart->getGroup()->getGame();
            }
        }

        //----- Maj group
        foreach ($groupList as $group) {
            $playerGroupRepository->maj($group);
        }

        //----- Maj game
        foreach ($gameList as $game) {
            $playerGameRepository->maj($game);
            $playerBadgeRepository->majMasterBadge($game);
        }

        //----- Maj player
        foreach ($playerList as $player) {
            $playerRepository->maj($player);
            $countryList[$player->getCountry()->getId()] = $player->getCountry();
        }

        //----- Maj rank country
        foreach ($countryList as $country) {
            $playerRepository->majRankCountry($country);
            $playerBadgeRepository->majCountryBadge($country);
        }

        //----- Maj all players
        $playerRepository->majRankPointChart();
        $playerRepository->majRankPointGame();
        $playerRepository->majRankMedal();
        $playerRepository->majRankCup();
        $playerRepository->majRankProof();
    }


    /**
     * @param OutputInterface $output
     * @throws \Exception
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
        /** @var \VideoGamesRecords\CoreBundle\Repository\TeamRepository $teamRepository */
        $teamRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Team');
        /** @var \VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository $teamBadgeRepository */
        $teamBadgeRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:TeamBadge');

        if (false === $chartRepository->isMajTeamRunning()) {
            $chartRepository->goToMajTeam(self::NB_CHART_TO_MAJ);

            $teamList = array();
            $groupList = array();
            $gameList = array();

            $charts = $chartRepository->getChartToMajTeam();

            if (count($charts) > 0) {
                foreach ($charts as $chart) {
                    $groupId = $chart->getGroup()->getId();
                    $gameId = $chart->getGroup()->getGame()->getId();
                    $teamList = array_unique(
                        array_merge($teamList, $teamChartRepository->maj($chart->getId()))
                    );

                    //----- Group
                    if (!isset($groupList[$groupId])) {
                        $groupList[$groupId] = $chart->getGroup();
                    }
                    //----- Game
                    if (!isset($gameList[$gameId])) {
                        $gameList[$gameId] = $chart->getGroup()->getGame();
                    }
                }

                //----- Maj group
                foreach ($groupList as $group) {
                    $teamGroupRepository->maj($group);
                }

                //----- Maj game
                foreach ($gameList as $game) {
                    $teamGameRepository->maj($game);
                    $teamBadgeRepository->majMasterBadge($game);
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
                $output->writeln('No record to maj');
            }
        } else {
            $output->writeln('vgr:chart maj-team is allready running');
        }
    }
}
