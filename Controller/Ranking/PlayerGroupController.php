<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerGroupRanking;

/**
 * Class PlayerGroupController
 */
class PlayerGroupController extends DefaultController
{
    private PlayerGroupRanking $playerGroupRanking;

    public function __construct(PlayerGroupRanking $playerGroupRanking)
    {
        $this->playerGroupRanking = $playerGroupRanking;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        $idTeam = $request->query->get('idTeam', null);
        $options = [
            'maxRank' => $request->query->get('maxRank', 5),
            'player' => $this->getPlayer(),
            'team' => $idTeam ? $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam) : null,
        ];
        return $this->playerGroupRanking->getRankingPoints($game->getId(), $options);
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingMedals(Game $game, Request $request): array
    {
        return $this->playerGroupRanking->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'player' => $this->getPlayer(),
            ]
        );
    }


}
