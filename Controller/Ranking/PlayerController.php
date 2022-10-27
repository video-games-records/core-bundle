<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Service\Ranking\Select\PlayerRankingSelect;

/**
 * Class PlayerController
 */
class PlayerController extends AbstractController
{
    private PlayerRankingSelect $playerRankingSelect;

    public function __construct(PlayerRankingSelect $playerRankingSelect)
    {
        $this->playerRankingSelect = $playerRankingSelect;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getRankingPointChart(Request $request)
    {
        return $this->playerRankingSelect->getRankingPointChart(
            null,
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }

     /**
     * @param Request $request
     * @return array
     */
    public function getRankingPointGame(Request $request)
    {
        return $this->playerRankingSelect->getRankingPointGame(
            null,
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getRankingMedals(Request $request): array
    {
        return $this->playerRankingSelect->getRankingMedals(
            null,
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }
}
