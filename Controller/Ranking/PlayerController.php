<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerRankingSelect;

/**
 * Class PlayerController
 */
class PlayerController extends DefaultController
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
            $this->getOptions($request)
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
            $this->getOptions($request)
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
            $this->getOptions($request)
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getOptions(Request $request): array
    {
        $idTeam = $request->query->get('idTeam', null);
        return [
            'maxRank' => $request->query->get('maxRank', 5),
            'player' => $this->getPlayer(),
            'team' => $idTeam ? $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam) : null,
        ];
    }
}
