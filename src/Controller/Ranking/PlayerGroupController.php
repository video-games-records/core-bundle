<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlayerGroupRankingQuery;

/**
 * Class PlayerGroupController
 */
class PlayerGroupController extends AbstractController
{
    private PlayerGroupRankingQuery $playerGroupRankingQuery;

    public function __construct(PlayerGroupRankingQuery $playerGroupRankingQuery)
    {
        $this->playerGroupRankingQuery = $playerGroupRankingQuery;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->playerGroupRankingQuery->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
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
        return $this->playerGroupRankingQuery->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }
}
