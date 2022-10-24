<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Select\PlayerGameRankingSelect;

/**
 * Class PlayerGameController
 */
class PlayerGameController extends DefaultController
{
    private PlayerGameRankingSelect $playerGameRankingSelect;

    public function __construct(PlayerGameRankingSelect $playerGameRankingSelect)
    {
        $this->playerGameRankingSelect = $playerGameRankingSelect;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        $idTeam = $request->query->get('idTeam', null);
        return $this->playerGameRankingSelect->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'player' => $this->getPlayer(),
                'team' => $idTeam ? $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam) : null,
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
        return $this->playerGameRankingSelect->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'player' => $this->getPlayer(),
            ]
        );
    }
}
