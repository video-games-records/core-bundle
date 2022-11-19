<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\TeamGameRankingQuery;

/**
 * Class TeamGameController
 */
class TeamGameController extends AbstractController
{
    private TeamGameRankingQuery $teamGameRankingQuery;

    public function __construct(TeamGameRankingQuery $teamGameRankingQuery)
    {
        $this->teamGameRankingQuery = $teamGameRankingQuery;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->teamGameRankingQuery->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingMedals(Game $game, Request $request): array
    {
        return $this->teamGameRankingQuery->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
