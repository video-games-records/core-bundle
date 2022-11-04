<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlayerRankingSelect;

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
     * @throws ORMException
     */
    public function getRankingPointChart(Request $request): array
    {
        return $this->playerRankingSelect->getRankingPointChart(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPointGame(Request $request): array
    {
        return $this->playerRankingSelect->getRankingPointGame(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingMedals(Request $request): array
    {
        return $this->playerRankingSelect->getRankingMedals(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingBadge(Request $request): array
    {
        return $this->playerRankingSelect->getRankingBadge(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingCup(Request $request): array
    {
        return $this->playerRankingSelect->getRankingCup(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingProof(Request $request): array
    {
        return $this->playerRankingSelect->getRankingProof(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }
}
