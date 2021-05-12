<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Repository\ChartRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamChartRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamGameRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamGroupRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

class ChartCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:chart';

    private $em;
    private $nbChartToMaj = 1000;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
    }

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
                'nbChartToMaj',
                null,
                InputOption::VALUE_REQUIRED,
                'Nb Chart to MAJ'
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
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        $idChart  = $input->getOption('idChart');
        if ($input->getOption('nbChartToMaj')) {
            $this->nbChartToMaj = $input->getOption('nbChartToMaj');
        }

        switch ($function) {
            case 'maj-player':
                if ($idChart) {
                    $chart = $this->em->getRepository('VideoGamesRecordsCoreBundle:Chart')->find($idChart);
                    $this->updatePlayerChart([$chart]);
                } else {
                    $this->majPlayer($output);
                }
                break;
            case 'maj-team':
                if ($idChart) {
                    $chart = $this->em->getRepository('VideoGamesRecordsCoreBundle:Chart')->find($idChart);
                    $this->updateTeamChart([$chart]);
                } else {
                    $this->majTeam($output);
                }
                break;
        }
        $this->end($output);

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @throws DBALException
     * @throws ExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function majPlayer(OutputInterface $output)
    {
        /** @var ChartRepository $chartRepository */
        $chartRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Chart');

        if ($chartRepository->isMajPlayerRunning()) {
            $output->writeln('vgr:chart maj-player is already running');
            return;
        }
        $chartRepository->goToMajPlayer($this->nbChartToMaj);

        $charts = $chartRepository->getChartToMajPlayer();
        $this->updatePlayerChart($charts);

        $output->writeln(sprintf('%d chart(s) updated', count($charts)));
    }

    /**
     * @param array $charts
     * @throws ExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function updatePlayerChart(array $charts)
    {
        /** @var PlayerChartRepository $playerChartRepository */
        $playerChartRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerChart');
        /** @var PlayerGroupRepository $playerGroupRepository */
        $playerGroupRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup');
        /** @var PlayerGameRepository $playerGameRepository */
        $playerGameRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerGame');
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Player');
        /** @var PlayerBadgeRepository $playerBadgeRepository */
        $playerBadgeRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge');

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
            if ($player->getCountry()) {
                $countryList[$player->getCountry()->getId()] = $player->getCountry();
            }
        }

        //----- Maj rank country
        foreach ($countryList as $country) {
            $playerRepository->majRankCountry($country);
            $playerBadgeRepository->majCountryBadge($country);
        }
    }


    /**
     * @param OutputInterface $output
     * @throws DBALException
     * @throws ExceptionInterface
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function majTeam(OutputInterface $output)
    {
        /** @var ChartRepository $chartRepository */
        $chartRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Chart');

        if ($chartRepository->isMajTeamRunning()) {
            $output->writeln('vgr:chart maj-team is already running');
            return;
        }
        $chartRepository->goToMajTeam($this->nbChartToMaj);

        $charts = $chartRepository->getChartToMajTeam();
        $this->updateTeamChart($charts);

        $output->writeln(sprintf('%d chart(s) updated', count($charts)));
    }


    /**
     * @param array $charts
     * @throws ExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws DBALException
     */
    public function updateTeamChart(array $charts)
    {
        /** @var TeamChartRepository $teamChartRepository */
        $teamChartRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamChart');
        /** @var TeamGroupRepository $teamGroupRepository */
        $teamGroupRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamGroup');
        /** @var TeamGameRepository $teamGameRepository */
        $teamGameRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamGame');
        /** @var TeamRepository $teamRepository */
        $teamRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Team');
        /** @var TeamBadgeRepository $teamBadgeRepository */
        $teamBadgeRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamBadge');

        $teamList = array();
        $groupList = array();
        $gameList = array();

        foreach ($charts as $chart) {
            $groupId = $chart->getGroup()->getId();
            $gameId = $chart->getGroup()->getGame()->getId();
            $teamList = array_unique(
                array_merge($teamList, $teamChartRepository->maj($chart))
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

        //----- Maj team
        foreach ($teamList as $team) {
            $teamRepository->maj($team);
        }
    }
}
