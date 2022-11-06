<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlayerSerieRankingQuery;

/**
 * Class PlayerSerieController
 */
class PlayerSerieController extends AbstractController
{
    private PlayerSerieRankingQuery $playerSerieRankingQuery;

    public function __construct(PlayerSerieRankingQuery $playerSerieRankingQuery)
    {
        $this->playerSerieRankingQuery = $playerSerieRankingQuery;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->playerSerieRankingQuery->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
                'limit' => $request->query->get('limit', 100),
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
        return $this->playerSerieRankingQuery->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
                'limit' => $request->query->get('limit', 100),
            ]
        );
    }
}
