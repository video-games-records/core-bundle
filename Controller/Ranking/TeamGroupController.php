<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Select\TeamGroupRankingSelect;

/**
 * Class TeamGroupController
 */
class TeamGroupController extends DefaultController
{
    private TeamGroupRankingSelect $teamGroupRankingSelect;

    public function __construct(TeamGroupRankingSelect $teamGroupRankingSelect)
    {
        $this->teamGroupRankingSelect = $teamGroupRankingSelect;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->teamGroupRankingSelect->getRankingPoints(
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
        return $this->teamGroupRankingSelect->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'team' => $this->getTeam(),
            ]
        );
    }
}
