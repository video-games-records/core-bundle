<?php

namespace VideoGamesRecords\CoreBundle\Command;

use ProjetNormandie\CommonBundle\Command\DefaultCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Entity\Chart;

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
                null,
                InputOption::VALUE_NONE,
                'Debug option (sql)'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool|int|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        $idChart  = $input->getOption('idChart');

        switch ($function) {
            case 'maj-player':
                if ($idChart) {
                    $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->maj($idChart);
                } else {
                    $this->majPlayer($output);
                }
                break;
        }
        $this->end($output);

        return true;
    }

    /**
     * @param OutputInterface $output
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
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
        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository $playerBadgeRepository */
        $playerBadgeRepository = $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge');

        if (false === $chartRepository->isMajPlayerRunning()) {
            $chartRepository->goToMajPlayer(self::NB_CHART_TO_MAJ);

            $playerList = [];
            $groupList  = [];
            $gameList   = [];

            $charts = $chartRepository->getChartToMajPlayer();

            if (count($charts) > 0) {
                foreach ($charts as $chart) {
                    $idGroup = $chart->getIdGroup();
                    $idGame  = $chart->getGroup()->getIdGame();
                    //----- Player
                    $playerList = array_unique(
                        array_merge($playerList, $playerChartRepository->maj($chart->getId()))
                    );
                    //----- Group
                    if (!isset($groupList[$idGroup])) {
                        $groupList[$idGroup] = $idGroup;
                    }
                    //----- Game
                    if (!isset($gameList[$idGame])) {
                        $gameList[$idGame] = $idGame;
                    }
                }

                //----- Maj group
                foreach ($groupList as $idGroup) {
                    $playerGroupRepository->maj($idGroup);
                }

                //----- Maj game
                foreach ($gameList as $idGame) {
                    $playerGameRepository->maj($idGame);
                    $playerBadgeRepository->majMasterBadge($idGame);
                }

                //----- Maj player
                foreach ($playerList as $idPlayer) {
                    $playerRepository->maj($idPlayer);
                }

                //----- Maj all players
                $playerRepository->majRankPointChart();
                $playerRepository->majRankPointGame();
                $playerRepository->majRankMedal();
                $playerRepository->majRankCup();
                $playerRepository->majRankProof();
                //@todo MAJ badge best player on country
            } else {
                $output->writeln('No chart to be updated');
            }
        } else {
            $output->writeln('vgr:chart maj-player is already running');
        }
    }
}
