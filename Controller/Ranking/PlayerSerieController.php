<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerSerieRanking;

/**
 * Class PlayerSerieController
 */
class PlayerSerieController extends DefaultController
{
    private PlayerSerieRanking $playerSerieRanking;

    public function __construct(PlayerSerieRanking $playerSerieRanking)
    {
        $this->playerSerieRanking = $playerSerieRanking;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->playerSerieRanking->getRankingPoints(
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
        return $this->playerSerieRanking->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
                'limit' => $request->query->get('limit', 100),
                'player' => $this->getPlayer(),
            ]
        );
    }


}
