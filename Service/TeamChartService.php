<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Repository\TeamChartRepository;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\TeamGameRankingUpdater;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\TeamGroupRankingUpdater;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\TeamRankingUpdater;

class TeamChartService
{
    private TeamGameRankingUpdater $teamGameRankingUpdater;
    private TeamGroupRankingUpdater $teamGroupRankingUpdater;
    private ChartService $chartService;
    private TeamChartRepository $teamChartRepository;
    private TeamRankingUpdater $teamRankingUpdater;

    public function __construct(
        TeamGameRankingUpdater $teamGameRankingUpdater,
        TeamGroupRankingUpdater $teamGroupRankingUpdater,
        ChartService $chartService,
        TeamChartRepository $teamChartRepository,
        TeamRankingUpdater $teamRankingUpdater
    ) {
        $this->teamGameRankingUpdater = $teamGameRankingUpdater;
        $this->teamGroupRankingUpdater = $teamGroupRankingUpdater;
        $this->chartService = $chartService;
        $this->teamChartRepository = $teamChartRepository;
        $this->teamRankingUpdater = $teamRankingUpdater;
    }


    /**
     * @param int $nbChartToMaj
     * @return int
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExceptionInterface
     */
    public function majRanking(int $nbChartToMaj = 100): int
    {
        $this->chartService->goToMajTeam($nbChartToMaj);
        $charts = $this->chartService->getChartToMajTeam();

        $teamList = array();
        $groupList = array();
        $gameList = array();

        foreach ($charts as $chart) {
            $groupId = $chart->getGroup()->getId();
            $gameId = $chart->getGroup()->getGame()->getId();
            $teamList = array_unique(
                array_merge($teamList, $this->teamChartRepository->maj($chart))
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
            $this->teamGroupRankingUpdater->maj($group->getId());
        }

        //----- Maj game
        foreach ($gameList as $game) {
            $this->teamGameRankingUpdater->maj($game->getId());
        }

        //----- Maj team
        foreach ($teamList as $team) {
            $this->teamRankingUpdater->maj($team);
        }
        return count($charts);
    }
}
