<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\TeamGroupRankingQuery;

/**
 * Class TeamGroupController
 */
class TeamGroupController extends AbstractController
{
    private TeamGroupRankingQuery $teamGroupRankingQuery;

    public function __construct(TeamGroupRankingQuery $teamGroupRankingQuery)
    {
        $this->teamGroupRankingQuery = $teamGroupRankingQuery;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->teamGroupRankingQuery->getRankingPoints(
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
        return $this->teamGroupRankingQuery->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
