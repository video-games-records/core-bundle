<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Repository\TeamChartRepository;
use VideoGamesRecords\CoreBundle\Service\Ranking\Update\TeamGameRankingUpdate;
use VideoGamesRecords\CoreBundle\Service\Ranking\Update\TeamGroupRankingUpdate;
use VideoGamesRecords\CoreBundle\Service\Ranking\Update\TeamRankingUpdate;

class TeamChartService
{
    private TeamGameRankingUpdate $teamGameRankingUpdate;
    private TeamGroupRankingUpdate $teamGroupRankingUpdate;
    private ChartService $chartService;
    private TeamChartRepository $teamChartRepository;
    private TeamRankingUpdate $teamRankingUpdate;

    public function __construct(
        TeamGameRankingUpdate $teamGameRankingUpdate,
        TeamGroupRankingUpdate $teamGroupRankingUpdate,
        ChartService $chartService,
        TeamChartRepository $teamChartRepository,
        TeamRankingUpdate $teamRankingUpdate
    ) {
        $this->teamGameRankingUpdate = $teamGameRankingUpdate;
        $this->teamGroupRankingUpdate = $teamGroupRankingUpdate;
        $this->chartService = $chartService;
        $this->teamChartRepository = $teamChartRepository;
        $this->teamRankingUpdate = $teamRankingUpdate;
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
            $this->teamGroupRankingUpdate->maj($group->getId());
        }

        //----- Maj game
        foreach ($gameList as $game) {
            $this->teamGameRankingUpdate->maj($game->getId());
        }

        //----- Maj team
        foreach ($teamList as $team) {
            $this->teamRankingUpdate->maj($team);
        }
        return count($charts);
    }
}
