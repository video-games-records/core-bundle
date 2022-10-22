<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerRanking;

/**
 * Class PlayerController
 */
class PlayerController extends DefaultController
{
    private PlayerRanking $playerRanking;

    public function __construct(PlayerRanking $playerRanking)
    {
        $this->playerRanking = $playerRanking;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getRankingPointChart(Request $request)
    {
        return $this->playerRanking->getRankingPointChart(
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
        return $this->playerRanking->getRankingPointGame(
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
        return $this->playerRanking->getRankingMedals(
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
