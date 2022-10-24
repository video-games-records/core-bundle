<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Select\PlayerSerieRankingSelect;

/**
 * Class PlayerSerieController
 */
class PlayerSerieController extends DefaultController
{
    private PlayerSerieRankingSelect $playerSerieRankingSelect;

    public function __construct(PlayerSerieRankingSelect $playerSerieRankingSelect)
    {
        $this->playerSerieRankingSelect = $playerSerieRankingSelect;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->playerSerieRankingSelect->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
                'limit' => $request->query->get('limit', 100),
                'player' => $this->getPlayer(),
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
        return $this->playerSerieRankingSelect->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
                'limit' => $request->query->get('limit', 100),
                'player' => $this->getPlayer(),
            ]
        );
    }
}
