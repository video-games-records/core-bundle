<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Select\TeamGameRankingSelect;

/**
 * Class TeamGameController
 */
class TeamGameController extends AbstractController
{
    private TeamGameRankingSelect $teamGameRankingSelect;

    public function __construct(TeamGameRankingSelect $teamGameRankingSelect)
    {
        $this->teamGameRankingSelect = $teamGameRankingSelect;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->teamGameRankingSelect->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
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
        return $this->teamGameRankingSelect->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
