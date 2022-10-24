<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Select\PlayerGroupRankingSelect;

/**
 * Class PlayerGroupController
 */
class PlayerGroupController extends DefaultController
{
    private PlayerGroupRankingSelect $playerGroupRankingSelect;

    public function __construct(PlayerGroupRankingSelect $playerGroupRankingSelect)
    {
        $this->playerGroupRankingSelect = $playerGroupRankingSelect;
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
        return $this->playerGroupRankingSelect->getRankingPoints($game->getId(), $options);
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingMedals(Game $game, Request $request): array
    {
        return $this->playerGroupRankingSelect->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'player' => $this->getPlayer(),
            ]
        );
    }
}
