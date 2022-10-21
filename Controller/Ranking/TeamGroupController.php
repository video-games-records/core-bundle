<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\TeamGroupRanking;

/**
 * Class TeamGroupController
 */
class TeamGroupController extends DefaultController
{
    private TeamGroupRanking $teamGroupRanking;

    public function __construct(TeamGroupRanking $teamGroupRanking)
    {
        $this->teamGroupRanking = $teamGroupRanking;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->teamGroupRanking->getRankingPoints(
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
        return $this->teamGroupRanking->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'team' => $this->getTeam(),
            ]
        );
    }
}
