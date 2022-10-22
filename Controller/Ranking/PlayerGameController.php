<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerGameRanking;

/**
 * Class PlayerGameController
 */
class PlayerGameController extends DefaultController
{
    private PlayerGameRanking $playerGameRanking;

    public function __construct(PlayerGameRanking $playerGameRanking)
    {
        $this->playerGameRanking = $playerGameRanking;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        $idTeam = $request->query->get('idTeam', null);
        return $this->playerGameRanking->getRankingPoints(
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
        return $this->playerGameRanking->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'player' => $this->getPlayer(),
            ]
        );
    }


}
