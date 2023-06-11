<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerRankingQuery;

/**
 * Class PlayerController
 */
class PlayerController extends AbstractController
{
    private PlayerRankingQuery $playerRankingQuery;

    public function __construct(PlayerRankingQuery $playerRankingQuery)
    {
        $this->playerRankingQuery = $playerRankingQuery;
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPointChart(Request $request): array
    {
        return $this->playerRankingQuery->getRankingPointChart(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
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
        return $this->playerRankingQuery->getRankingPointGame(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
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
        return $this->playerRankingQuery->getRankingMedals(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
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
        return $this->playerRankingQuery->getRankingBadge(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
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
        return $this->playerRankingQuery->getRankingCup(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
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
        return $this->playerRankingQuery->getRankingProof(
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
            ]
        );
    }
}
