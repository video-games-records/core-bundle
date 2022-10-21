<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerGameRanking;
use VideoGamesRecords\CoreBundle\Service\Ranking\TeamGameRanking;

/**
 * Class TeamGameController
 */
class TeamGameController extends DefaultController
{
    private TeamGameRanking $teamGameRanking;

    public function __construct(TeamGameRanking $teamGameRanking)
    {
        $this->teamGameRanking = $teamGameRanking;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->teamGameRanking->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'team' => $this->getTeam(),
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
        return $this->teamGameRanking->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'team' => $this->getTeam(),
            ]
        );
    }
}
