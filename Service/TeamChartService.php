<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Exception;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Repository\TeamChartRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;
use VideoGamesRecords\CoreBundle\Service\Ranking\TeamGameRanking;
use VideoGamesRecords\CoreBundle\Service\Ranking\TeamGroupRanking;

class TeamChartService
{
    private TeamGameRanking $teamGameRanking;
    private TeamGroupRanking $teamGroupRanking;
    private GameService $gameService;
    private ChartService $chartService;
    private TeamChartRepository $teamChartRepository;
    private TeamRepository $teamRepository;

    public function __construct(
        TeamGameRanking $teamGameRanking,
        TeamGroupRanking $teamGroupRanking,
        GameService $gameService,
        ChartService $chartService,
        TeamChartRepository $teamChartRepository,
        TeamRepository $teamRepository
    ) {
        $this->teamGameRanking = $teamGameRanking;
        $this->teamGroupRanking = $teamGroupRanking;
        $this->gameService = $gameService;
        $this->chartService = $chartService;
        $this->teamChartRepository = $teamChartRepository;
        $this->teamRepository = $teamRepository;
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
            $this->teamGroupRanking->maj($group->getId());
        }

        //----- Maj game
        foreach ($gameList as $game) {
            $this->teamGameRanking->maj($game->getId());
            $this->gameService->majTeamMasterBadge($game->getId());
        }

        //----- Maj team
        foreach ($teamList as $team) {
            $this->teamRepository->maj($team);
        }
        return count($charts);
    }
}
